<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class OTPCollection extends Model
{
    public string $phone;
    public string $otp;
    public $created_at;

    protected $guarded = ['created_at'];

    use HasFactory;
}
