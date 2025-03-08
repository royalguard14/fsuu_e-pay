<?php

namespace Database\Seeders;

use App\Models\GcashInformation;
use Illuminate\Database\Seeder;

class GcashInformationSeeder extends Seeder
{
    public function run()
    {
        GcashInformation::create([
            'account_name' => 'Ghaizar A. Bautista',
            'account_number' => '09277294457',
            'notes' => 'Primary GCASH account for payments'
        ]);
    }
}
