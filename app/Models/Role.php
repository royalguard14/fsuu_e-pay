<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends Model
{
       use HasFactory;



protected static $logAttributes = ['role_name', 'modules'];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated';

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Role $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Role deleted");
    });
}







  protected $table = 'roles'; 
    protected $fillable = ['role_name', 'modules'];

       protected $casts = [
        'modules' => 'array',  // Cast the modules field to an array
    ];

    
    
    public function users()
    {
        return $this->hasMany(User::class);
    }


   public function modules()
{
    return $this->belongsToMany(Module::class);
}


}
