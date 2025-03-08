<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EnrollmentHistory extends Model
{
    use HasFactory;




protected static $logAttributes = [
    'user_id',
    'grade_level_id',
    'section_id',
    'academic_year_id',
    'enrollment_date',
];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated';

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Enrollment History $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Enrollment History deleted");
    });
}






    

    protected $fillable = [
        'user_id',
        'grade_level_id',
        'section_id',
        'academic_year_id',
        'enrollment_date',
    ];

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

 public function section()
{
    return $this->belongsTo(Section::class);
}

public function payments()
{
    return $this->hasMany(Payment::class, 'enrollment_histories_id');
}





    
}
