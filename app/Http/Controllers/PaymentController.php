<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\EnrollmentHistory;
use App\Models\AcademicYear;
use App\Models\FeeBreakdown;
use App\Models\Payment;
use App\Models\GcashInformation;
use App\Models\GcashTransaction;


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
            'notes' => $request->notes ?? null,
            'cashier_id' => auth()->id()
            
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




public function getPaymentDetails($userId)
{
    $currentAcademicYear = AcademicYear::where('current', true)->first();

    $enrollment = EnrollmentHistory::where('user_id', $userId)
        ->where('academic_year_id', $currentAcademicYear->id)
        ->first();

    if (!$enrollment) {
        return response()->json(['message' => 'No enrollment found'], 404);
    }

    $feeBreakdown = FeeBreakdown::where('grade_level_id', $enrollment->grade_level_id)
        ->where('academic_year_id', $currentAcademicYear->id)
        ->first();

    $totalFees = $feeBreakdown->tuition_fee + collect(json_decode($feeBreakdown->other_fees))->sum();

  



$monthlyPayment = $enrollment->scholar 
    ? round($totalFees * 0.2683 / 10) 
    : round($totalFees / 10);

    $totalPaid = Payment::where('user_id', $userId)
        ->where('enrollment_history_id', $enrollment->id)
        ->sum('amount_paid');



    #$remainingBalance = $totalFees - $totalPaid;

    $remainingBalance = $enrollment->scholar
    ? round($totalFees  * 0.2683) 
    : $totalFees - $totalPaid;

    $months = [
        'June', 'July', 'August', 'September', 'October', 'November',
        'December', 'January', 'February', 'March'
    ];

    $monthlyPayments = [];
    $paidAmount = $totalPaid;
    $unpaidBalance = 0;

    foreach ($months as $index => $month) {
        $year = $index < 7 ? $currentAcademicYear->start : $currentAcademicYear->end;
        $due = $monthlyPayment;

        if ($paidAmount >= $due) {
            $status = 'Paid';
            $paidAmount -= $due;
            $due = 0; // Set amount to ₱0 if fully paid
        } else {
            if ($paidAmount > 0) {
                $due -= $paidAmount;
                $paidAmount = 0;
            }
            $status = $due > 0 ? 'Unpaid' : 'Paid';
        }

        // Add unpaid amounts only for months marked as unpaid
        if ($status === 'Unpaid') {
            $unpaidBalance += $due;
        }

        $monthlyPayments[] = [
            'month' => "$month $year",
            'amount' => number_format($due),
            'status' => $status
        ];
    }

    return response()->json([
        'totalPaid' => number_format($totalPaid),
        'totalBalance' => number_format($remainingBalance),
        'suggestedAmount' => number_format($unpaidBalance), // Add suggested amount
        'monthlyPayments' => $monthlyPayments
    ]);
}





////////////////////////

public function student()
{
     return view('payment.student');
}





public function payViaGcash(Request $request)
    {
        $activeGcash = GcashInformation::where('isActive', true)->first();

        if (!$activeGcash) {
            return redirect()->route('gcash_transactions.index')->with([
                'success' => 'No active GCash account found!',
                'icon' => 'error'
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:50',
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Store the receipt image
        $receiptPath = $request->file('receipt')->store('receipts', 'public');

        // Create the GCash transaction
        GcashTransaction::create([
            'user_id' => auth()->id(),
            'gcash_information_id' => $activeGcash->id, // Automatically use active GCash
            'amount' => $request->amount,
            'reference_number' => $request->reference_number,
            'receipt' => $receiptPath,
            'status' => 'pending'
        ]);

        return redirect()->route('payment.student')->with([
            'success' => 'Transaction submitted successfully!',
            'icon' => 'success'
        ]);
    }
























}
