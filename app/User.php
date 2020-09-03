<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    const ROLES = ["student", "teacher", "admin"];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', "role"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function toList(array $users)
    {
        return array_map("self::makeShort", $users);
    }

    public static function makeShort($user)
    {
        return [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
        ];
    }

    protected function isStudent()
    {
        return $this->role == self::ROLES[0];
    }
    protected function isTeacher()
    {
        return $this->role == self::ROLES[1];
    }

    public function createAdmin(array $details): self
    {
        $user = new self($details);

        $user->role = self::ROLES[2];
        $user->password = bcrypt($user->password);

        $user->save();

        return $user;
    }
}
