<?php
namespace App\Http\Controllers;
use App\Models\GcashTransaction;
use App\Models\GcashInformation;
use App\Models\AcademicYear;
use App\Models\EnrollmentHistory;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
class GcashTransactionController extends Controller
{
    

    public function getActive() {
        $transactions = GcashInformation::where('isActive', true)->latest()->get(['account_name','account_number','qr_code']);
        return response()->json($transactions);
    }
    

    public function allpending() {
        $pendings = GcashTransaction::where('status', 'pending')->with('gcashInformation','profile')->get();
        return response()->json($pendings);
    }

    
    public function store(Request $request) {
        $request->validate([
            'gcash_information_id' => 'required|exists:gcash_information,id',
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:50',
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $receiptPath = $request->file('receipt')->store('receipts', 'public');
        GcashTransaction::create([
            'user_id' => auth()->id(), // Authenticated student
            'gcash_information_id' => $request->gcash_information_id,
            'amount' => $request->amount,
            'reference_number' => $request->reference_number,
            'receipt' => $receiptPath,
            'status' => 'pending'
        ]);
        return redirect()->route('gcash_transactions.index')->with('success', 'Transaction submitted successfully!');
    }
    

    public function updateStatus(Request $request)
    {
        $request->validate([
            'reference_number' => 'required|string|exists:gcash_transactions,reference_number',
            'status' => 'required|in:approved,rejected'
        ]);
        $GcashTransaction = GcashTransaction::where('reference_number', $request->reference_number)->first();
        if (!$GcashTransaction) {
            return response()->json([
                'error' => 'Payment not found.',
                'icon' => 'error'
            ], 500);
        }
        try {
            
            $currentAcademicYear = AcademicYear::where('current', true)->first();

            $enrollment = EnrollmentHistory::where('user_id', $GcashTransaction->user_id)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->first();




            if (!$enrollment) {
             return response()->json([
                'success' => 'No active enrollment found!',
                'icon' => 'error'
            ], 404);
         }


$GcashTransaction->update(['status' => $request->status]);
         $payment = Payment::create([
            'user_id' => $GcashTransaction->user_id,
            'enrollment_history_id' => $enrollment->id,
            'amount_paid' => $GcashTransaction->amount,
            'payment_method' => 'Gcash',
            'payment_date' => $GcashTransaction->created_at,
            'reference_number' => $GcashTransaction->reference_number,
            'notes' => $GcashTransaction->receipt,
            'cashier_id' => auth()->id()
        ]);
         return response()->json([
            'success' => 'Payment status updated successfully.!',
            'icon' => 'success'
        ]);
     } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to update GCash payment.',
            'icon' => 'error'
        ], 500);
    }
}
}