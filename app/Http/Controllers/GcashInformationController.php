<?php

namespace App\Http\Controllers;

use App\Models\GcashInformation;
use App\Models\GcashTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class GcashInformationController extends Controller
{
    public function index()
    {
        $gcashInfos = GcashInformation::all();
        return view('gcash.index', compact('gcashInfos'));

    }

public function isActive($id)
{
    try {
        // Set all GCash accounts to inactive
        GcashInformation::query()->update(['isActive' => false]);

        // Find the GCash account to activate
        $gcashActive = GcashInformation::findOrFail($id);
        $gcashActive->update(['isActive' => true]);

        return response()->json([
            'success' => 'GCash account activated successfully!',
            'icon' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to activate GCash account.',
            'icon' => 'error'
        ], 500);
    }
}


  public function store(Request $request)
{
    $request->validate([
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|max:20|unique:gcash_information',
        'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // 2MB max
    ]);

    $data = $request->only(['account_name', 'account_number']);

    // Handle QR code upload
    if ($request->hasFile('qr_code')) {
        $path = $request->file('qr_code')->store('gcash_qrcodes', 'public');
        $data['qr_code'] = $path; // <-- Fixed here
    }

    GcashInformation::create($data);

    return redirect()->route('gcash.index')->with('success', 'Gcash info added successfully!');
}


    public function edit(GcashInformation $gcashInformation)
    {
        return view('gcash.edit', compact('gcashInformation'));
    }

      public function update(Request $request, GcashInformation $gcashInformation)
    {


        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20|unique:gcash_information,account_number,' . $gcashInformation->id,
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['account_name', 'account_number']);

        // Handle QR code upload and delete old one if exists
        if ($request->hasFile('qr_code')) {
            // Delete old QR code if exists
            if ($gcashInformation->qr_code) {
                Storage::disk('public')->delete($gcashInformation->qr_code);
            }

            $path = $request->file('qr_code')->store('gcash_qrcodes', 'public');
            $data['qr_code'] = $path;

        }


        $gcashInformation->update($data);

        return redirect()->route('gcash.index')->with('success', 'Gcash info updated successfully!');
    }
 

public function destroy(GcashInformation $gcashInformation)
{
    if ($gcashInformation->isActive) {
        return redirect()->route('gcash.index')->with([
            'success' => 'The currently active GCash account cannot be deleted.',
            'icon' => 'error'
        ]);
    }

    // Delete the QR code file if it exists
    if ($gcashInformation->qr_code) {
        Storage::disk('public')->delete($gcashInformation->qr_code);
    }

    // Delete the GCash information record
    $gcashInformation->delete();

    return redirect()->route('gcash.index')->with([
        'success' => 'GCash info deleted successfully!',
        'icon' => 'success'
    ]);
}


   public function mygcash()
    {

$userId = auth()->user()->id;
$gcashTransaction = GcashTransaction::where('user_id', $userId)
    ->orderBy('created_at', 'desc') // Optional: if you want the latest transactions first
    ->get();

return response()->json([
    'gcashTransaction' => $gcashTransaction
]);


    }



public function mywalkin()
{
    $userId = auth()->user()->id;

    $walkInPayments = Payment::where('user_id', $userId)
        ->where('payment_method', 'Walk-in') // Filter for Walk-in payments only
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'walkInPayments' => $walkInPayments
    ]);
}


}
