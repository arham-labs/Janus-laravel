<?php

namespace Arhamlabs\Authentication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthSetting extends Model
{
    use HasFactory;

    public $table = 'auth_settings';
    protected $fillable = ['uuid', 'model_name', 'model_id', 'user_type', 'user_status', 'registration_at', 'email_verified_at', 'last_login_at', 'last_logout_at'];


    public function userActivities()
    {
        return $this->morphMany(AuthUser::class, 'userActivityTable', 'model_name', 'model_id');
    }
}
