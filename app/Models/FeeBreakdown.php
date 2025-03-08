<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FeeBreakdown extends Model {
    use HasFactory;


  protected static $logAttributes = [
    'academic_year_id',
    'grade_level_id',
    'tuition_fee',
    'other_fees'
];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated';

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Fee Breakdown $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Fee Breakdown deleted");
    });
}






    protected $fillable = [
        'academic_year_id',
        'grade_level_id',
        'tuition_fee',
        'other_fees'
    ];

    protected $casts = [
        'other_fees' => 'json'
    ];

    public function academicYear() {
        return $this->belongsTo(AcademicYear::class);
    }

    public function gradeLevel() {
        return $this->belongsTo(GradeLevel::class);
    }
}
