<?php

namespace Arhamlabs\Authentication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'username',
        'sso_type',
        'email',
        'mobile',
        'password',
        'user_type',
        'country_code',
        'email_verified_at',
        'status'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
