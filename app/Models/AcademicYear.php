<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AcademicYear extends Model
{
    use HasFactory;




protected static $logAttributes = ['start', 'end', 'current'];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated'; 

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Academic Year $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Academic Year deleted");
    });
}







    protected $fillable = ['start', 'end', 'current'];
    

    public function enrollments()
    {
        return $this->hasMany(EnrollmentHistory::class);
    }

    // Scope to get the current academic year
    public function scopeCurrent($query)
    {
        return $query->where('current', true);
    }
}
