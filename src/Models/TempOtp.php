<?php

namespace Arhamlabs\Authentication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempOtp extends Model
{
    use HasFactory;

    public $table = 'temp_otp';

    protected $fillable = ['uuid','email','mobile', 'otp', 'expire_at', 'service'];

}
