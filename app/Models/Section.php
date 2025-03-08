<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Section extends Model
{
    use HasFactory;


   protected static $logAttributes = [
    'section_name',
    'adviser_id',
];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated';

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Section $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Section deleted");
    });
}



    protected $fillable = [
        'section_name',
        'adviser_id',
    ];

    // Relationship with User (Adviser)
    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    // Relationship with GradeLevel
    public function gradeLevels()
    {
        return $this->belongsToMany(GradeLevel::class, 'grade_level_section', 'section_id', 'grade_level_id');
    }
}
