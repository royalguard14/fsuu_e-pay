<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\EnrollmentHistory;
use App\Models\Payment;
use App\Models\GradeLevel;
use App\Models\FeeBreakdown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{
 

 public function developer()
 {
    $activityLogs = Activity::where('causer_id', '!=', Auth::id())
    ->latest()
    ->get();
// $activityLogs = Activity::latest()->get();
    $latestUsers = User::where('id', '!=', Auth::id())
    ->latest()
    ->take(8)
    ->get();
    $visitorData = Visitor::selectRaw('DATE(created_at) as date, COUNT(*) as count')
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->groupBy('date')
    ->orderBy('date', 'asc')
    ->get();
    $dates = $visitorData->pluck('date')->toArray();
    $counts = $visitorData->pluck('count')->toArray();
    return view('dashboard.developer', compact('dates', 'counts','activityLogs','latestUsers'));
}


// public function admin()
// {
//     $currentAcademicYear = AcademicYear::where('current', true)->first();
//     // Get all fee breakdowns for the current academic year
//     $feeBreakdowns = FeeBreakdown::where('academic_year_id', $currentAcademicYear->id)->get();
//     $totalExpected = $feeBreakdowns->sum(function ($fee) use ($currentAcademicYear) {
//         // Count students enrolled in this grade level for the current academic year
//         $studentsCount = EnrollmentHistory::where('academic_year_id', $currentAcademicYear->id)
//             ->where('grade_level_id', $fee->grade_level_id)
//             ->count();
//         // Sum the tuition fee and other fees
//         $otherFees = collect(json_decode($fee->other_fees, true))->sum();
//         $totalFeePerStudent = $fee->tuition_fee + $otherFees;
//         // Total expected for this grade level's students
//         return $studentsCount * $totalFeePerStudent;
//     });
//     // Total Money Collected
//     $totalCollected = Payment::whereHas('enrollmentHistory', function ($query) use ($currentAcademicYear) {
//         $query->where('academic_year_id', $currentAcademicYear->id);
//     })->sum('amount_paid');
//     // Number of Students this Academic Year
//     $totalStudents = EnrollmentHistory::where('academic_year_id', $currentAcademicYear->id)->count();
//     return view('dashboard.admin', compact('totalExpected', 'totalCollected', 'totalStudents', 'currentAcademicYear'));
// }


public function admin()
{
    try {
        $currentAcademicYear = AcademicYear::where('current', true)->first();

        if (!$currentAcademicYear) {
            throw new \Exception('No current academic year found.');
        }

        // Get all fee breakdowns for the current academic year
        $feeBreakdowns = FeeBreakdown::where('academic_year_id', $currentAcademicYear->id)->get();

        // Calculate Total Expected Money
        $totalExpected = $feeBreakdowns->sum(function ($fee) use ($currentAcademicYear) {
            $studentsCount = EnrollmentHistory::where('academic_year_id', $currentAcademicYear->id)
                ->where('grade_level_id', $fee->grade_level_id)
                ->count();
            $otherFees = collect(json_decode($fee->other_fees, true))->sum();
            return $studentsCount * ($fee->tuition_fee + $otherFees);
        });

        // Calculate Total Money Collected
        $totalCollected = Payment::whereHas('enrollmentHistory', function ($query) use ($currentAcademicYear) {
            $query->where('academic_year_id', $currentAcademicYear->id);
        })->sum('amount_paid');

        // Count Number of Students
        $totalStudents = EnrollmentHistory::where('academic_year_id', $currentAcademicYear->id)->count();

        // Prepare Data for Grade-Level Graph
        $grades = GradeLevel::whereHas('enrollmentHistories', function ($query) use ($currentAcademicYear) {
            $query->where('academic_year_id', $currentAcademicYear->id);
        })->get();

        $gradeLabels = [];
        $expectedCollections = [];
        $collectedMoney = [];

        foreach ($grades as $grade) {
            $gradeLabels[] = $grade->level;

            // Expected Collection for this grade
            $fee = $feeBreakdowns->where('grade_level_id', $grade->id)->first();
            if ($fee) {
                $studentsCount = EnrollmentHistory::where('academic_year_id', $currentAcademicYear->id)
                    ->where('grade_level_id', $grade->id)
                    ->count();
                $otherFees = collect(json_decode($fee->other_fees, true))->sum();
                $expectedCollections[] = $studentsCount * ($fee->tuition_fee + $otherFees);
            } else {
                $expectedCollections[] = 0;
            }

            // Collected Money for this grade
            $collected = Payment::whereHas('enrollmentHistory', function ($query) use ($currentAcademicYear, $grade) {
                $query->where('academic_year_id', $currentAcademicYear->id)
                    ->where('grade_level_id', $grade->id);
            })->sum('amount_paid');
            $collectedMoney[] = $collected;
        }

        // Data for daily collections chart
        $dailyCollections = Payment::whereHas('enrollmentHistory', function ($query) use ($currentAcademicYear) {
            $query->where('academic_year_id', $currentAcademicYear->id);
        })
            ->selectRaw('DATE(payment_date) as day, SUM(amount_paid) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $days = $dailyCollections->pluck('day');
        $dailyTotals = $dailyCollections->pluck('total');

        return view('dashboard.admin', compact(
            'totalExpected', 'totalCollected', 'totalStudents',
            'currentAcademicYear', 'gradeLabels', 'expectedCollections',
            'collectedMoney', 'days', 'dailyTotals'
        ));

    } catch (\Exception $e) {
        return redirect()->route('error')->with('error', $e->getMessage());
    }
}

}