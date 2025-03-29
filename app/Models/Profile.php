<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Profile extends Model
{
     use HasFactory;






protected static $logAttributes = [
    'user_id', 'firstname', 'lastname', 'phone_number', 'address', 
    'profile_picture', 'birthdate', 'gender', 'nationality', 'bio', 'lrn'
];

public static function boot()
{
    parent::boot();

    // Log when created or updated
    static::saved(function ($model) {
        $action = $model->wasRecentlyCreated ? 'created' : 'updated';

        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Profile $action");
    });

    // Log when deleted
    static::deleted(function ($model) {
        activity()->performedOn($model)
            ->causedBy(auth()->user())
            ->log("Profile deleted");
    });
}


     

  protected $table = 'profiles'; 
    protected $fillable = ['user_id', 'firstname', 'lastname', 'phone_number', 'address', 'profile_picture', 'birthdate', 'gender', 'nationality', 'bio','lrn',"profile_picture"];

    // Define the relationship with the User model
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}



        public function getFullNameAttribute()
    {
        $firstname = $this->firstname;
        $middlename = $this->middlename ?? '';  // Handle nullable middlename
        $lastname = $this->lastname;

        // Get the first letter of the middle name (if exists)
        $middleInitial = $middlename ? strtoupper(substr($middlename, 0, 1)) . '.' : '';

        return "{$firstname} {$middleInitial} {$lastname}";
    }
}
