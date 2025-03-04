<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users'; 

    protected $primaryKey = 'username';

    public $incrementing = false; // Karena primary key bukan auto-increment

    protected $keyType = 'integer'; // Primary key bertipe integer

    protected $guarded = [];

    protected $fillable = [
        'username',
        'name',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];
}
