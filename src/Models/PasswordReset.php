<?php

namespace Arhamlabs\Authentication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    protected $primaryKey = 'email';
    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];
    public $timestamps = false;
}
