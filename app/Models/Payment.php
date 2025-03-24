<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'enrollment_history_id',
        'user_id',
        'amount_paid',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'cashier_id',
    ];
//

  protected $casts = [
        'payment_date' => 'datetime'
    ];


public function enrollmentHistory()
{
    return $this->belongsTo(EnrollmentHistory::class);
}


}
