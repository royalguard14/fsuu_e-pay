<?php

namespace App\Http\Controllers;

use App\Models\GcashInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GcashInformationController extends Controller
{
    public function index()
    {
        $gcashInfos = GcashInformation::all();
        return view('gcash.index', compact('gcashInfos'));
    }

    public function create()
    {
        return view('gcash.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20|unique:gcash_information',
        ]);

        GcashInformation::create($request->all());

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
        ]);

        $gcashInformation->update($request->all());

        return redirect()->route('gcash.index')->with('success', 'Gcash info updated successfully!');
    }

    public function destroy(GcashInformation $gcashInformation)
    {
        $gcashInformation->delete();
        return redirect()->route('gcash.index')->with('success', 'Gcash info deleted successfully!');
    }
}
