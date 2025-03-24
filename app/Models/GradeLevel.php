<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;
use Spatie\Activitylog\Traits\LogsActivity;

class GradeLevel extends Model
{
    use HasFactory;

    protected static $logAttributes = ['level', 'section_ids'];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated';

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Grade Level $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Grade Level deleted");
    });
}






    

    protected $fillable = ['level', 'section_ids'];



protected $casts = [
    'section_ids' => 'array'
];

public function sections()
{
    return $this->belongsToMany(Section::class);
}

public function enrollmentHistories()
{
    return $this->hasMany(EnrollmentHistory::class, 'grade_level_id');
}













    
}
