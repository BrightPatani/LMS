<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens; // this trait is used for API token authentication, allowing users to generate and manage API tokens for authentication purposes.

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = ['role' => Role::class]; // this casts the 'role' attribute to the Role enum, ensuring that when you access the role of a user, it will be an instance of the Role enum rather than a raw string.

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

  
    public function coursesTeaching(): HasMany
    {
        return $this->hasMany(Course::class);
    } // this defines that a user can have many courses they are teaching, which is important for instructors.

    // Define relationship to courses enrolled by the user (if the user is a student)
    public function coursesEnrolled(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'enrollments', 'user_id', 'course_id' )->withTimestamps(); //take note of the parameters passed to hasMany, they are for defining the relationship through the enrollments table.
    } // this defines that a user can have many courses they are enrolled in, which is important for students.

    
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    } // this defines that a user has many enrollments, which is important for tracking which courses a student is enrolled in.

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    } // this defines that a user has many submissions, which is important for students submitting assignments.
}
