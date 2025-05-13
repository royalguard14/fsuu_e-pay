<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gcash_information_id',
        'amount',
        'reference_number',
        'receipt',
        'status',
        'reason',
    ];

    // Relationship with User
    public function user() {
        return $this->belongsTo(User::class);
    }


public function profile()
{
    return $this->hasOne(Profile::class, 'user_id', 'user_id');
}


    // Relationship with GcashInformation
    public function gcashInformation() {
        return $this->belongsTo(GcashInformation::class);
    }
}
