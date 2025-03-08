<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\EnrollmentHistory;
use App\Models\AcademicYear;
use App\Models\FeeBreakdown;
use App\Models\Payment;

class PaymentController extends Controller
{



///////////////////////////////////////////////////////////////////////////////////////////

    public function cashier()
    {
        return view('payment.cashier');
    }

    // Search students by LRN, firstname, or lastname
    public function search(Request $request)
    {
        $query = $request->input('query');

        $students = Profile::where('lrn', 'like', "%{$query}%")
            ->orWhere('firstname', 'like', "%{$query}%")
            ->orWhere('lastname', 'like', "%{$query}%")
            ->get(['user_id', 'lrn', 'firstname', 'lastname']);

        return response()->json($students);
    }

    // Get payment history for the selected student based on current academic year
    public function getPaymentHistory($userId, Request $request)
    {
        $currentAcademicYear = AcademicYear::where('current', true)->first();

        $payments = Payment::where('user_id', $userId)
            ->whereHas('enrollmentHistory', function ($query) use ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear->id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('reference_number', 'like', "%{$request->search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get(); 

        return response()->json($payments);
    }

    // Get fee breakdown for the student's current academic year and grade level
    public function getFeeBreakdown($userId)
    {
        $currentAcademicYear = AcademicYear::where('current', true)->first();

        $enrollment = EnrollmentHistory::where('user_id', $userId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->with('gradeLevel')
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'No active enrollment found'], 404);
        }

        $fee = FeeBreakdown::where('grade_level_id', $enrollment->grade_level_id)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->first();

        if (!$fee) {
            return response()->json([]);
        }

        $otherFees = json_decode($fee->other_fees, true);

        $breakdown = collect([['fee_type' => 'Tuition Fee', 'amount' => $fee->tuition_fee]]);

        foreach ($otherFees as $key => $amount) {
            $breakdown->push([
                'fee_type' => ucwords(str_replace('_', ' ', $key)),
                'amount' => $amount
            ]);
        }

        return response()->json($breakdown); // No manual pagination; DataTables will handle this
    }

    // Store walk-in payment
    public function storeWalkInPayment(Request $request, $userId)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'payment_method' => 'required|in:Walk-in'
        ]);

        $currentAcademicYear = AcademicYear::where('current', true)->first();

        $enrollment = EnrollmentHistory::where('user_id', $userId)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->first();

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'No active enrollment found'], 404);
        }

        $payment = Payment::create([
            'user_id' => $userId,
            'enrollment_history_id' => $enrollment->id,
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'payment_date' => now(),
            'reference_number' => strtoupper(uniqid()),
            'notes' => $request->notes ?? null
        ]);

        return response()->json(['success' => true, 'message' => 'Payment recorded successfully', 'payment' => $payment]);
    }



    public function getPreviousBalance($userId)
{
    $currentAcademicYear = AcademicYear::where('current', true)->first();

    $previousBalances = EnrollmentHistory::where('user_id', $userId)
        ->where('academic_year_id', '!=', $currentAcademicYear->id)
        ->with(['gradeLevel', 'payments'])
        ->get();

    $balance = 0;

    foreach ($previousBalances as $enrollment) {
        $fee = FeeBreakdown::where('grade_level_id', $enrollment->grade_level_id)
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->first();

        if ($fee) {
            $totalFees = $fee->tuition_fee + array_sum(json_decode($fee->other_fees, true));
            $totalPaid = $enrollment->payments->sum('amount_paid');

            $remaining = $totalFees - $totalPaid;
            if ($remaining > 0) {
                $balance += $remaining;
            }
        }
    }

    return response()->json(['previous_balance' => $balance]);
}




////////////////////////
    public function student ()
    {
          $currentAcademicYear = AcademicYear::where('current', true)->first();
   # $userId = auth()->id
           $userId = 4;

    $payments = Payment::where('user_id', $userId)
        ->whereHas('enrollmentHistory', function ($query) use ($currentAcademicYear) {
            $query->where('academic_year_id', $currentAcademicYear->id);
        })
        ->orderBy('created_at', 'desc')
        ->get();


    return view('payment.student', compact('payments'));
    }



public function studentFeeBreakdown()
{
    $currentAcademicYear = AcademicYear::where('current', true)->first();
    #$userId = auth()->id();
    $userId = 4;


    $enrollment = EnrollmentHistory::where('user_id', $userId)
        ->where('academic_year_id', $currentAcademicYear->id)
        ->with('gradeLevel')
        ->first();

    if (!$enrollment) {
        return response()->json(['message' => 'No active enrollment found'], 404);
    }

    $fee = FeeBreakdown::where('grade_level_id', $enrollment->grade_level_id)
        ->where('academic_year_id', $currentAcademicYear->id)
        ->first();

    if (!$fee) {
        return response()->json([]);
    }

    $otherFees = json_decode($fee->other_fees, true);

    $breakdown = collect([['fee_type' => 'Tuition Fee', 'amount' => $fee->tuition_fee]]);

    foreach ($otherFees as $key => $amount) {
        $breakdown->push([
            'fee_type' => ucwords(str_replace('_', ' ', $key)),
            'amount' => $amount
        ]);
    }

    return response()->json($breakdown);
}

public function payViaGcash(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'reference_number' => 'required|unique:payments',
    ]);

    $currentAcademicYear = AcademicYear::where('current', true)->first();
    #$userId = auth()->id();
    $userId = 4;
    $enrollment = EnrollmentHistory::where('user_id', $userId)
        ->where('academic_year_id', $currentAcademicYear->id)
        ->first();

    if (!$enrollment) {
        return response()->json(['success' => false, 'message' => 'No active enrollment found'], 404);
    }



    $payment = Payment::create([
        'user_id' => $userId,
        'enrollment_history_id' => $enrollment->id,
        'amount_paid' => $request->amount,
        'payment_method' => 'GCash',
        'payment_date' => now(),
        'reference_number' => $request->reference_number,
        'notes' => $request->notes ?? null
    ]);

    return response()->json(['success' => true, 'message' => 'Payment submitted successfully']);
}


}
