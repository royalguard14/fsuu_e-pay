<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GcashInformation extends Model
{
    use HasFactory;



       protected static $logAttributes = [
    'account_name',
    'account_number',
    'qr_code',
    'isActive'
];
protected $casts = [
    'isActive' => 'boolean',
];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated';

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Gcash $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Gcash deleted");
    });
}



    protected $table = 'gcash_information';

    protected $fillable = [
        'account_name',
        'account_number',
        'qr_code',
        'isActive'
    ];
}
