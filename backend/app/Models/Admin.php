<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory;

    /**
     * Table associated with the model.
     */
    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
    ];

    /**
     * Attributes hidden for arrays / JSON.
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Use password_hash as the auth password field.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}