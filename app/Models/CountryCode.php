<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class CountryCode extends Model
{
    use HasFactory;

    public string $name;
    public string $dial_code;
    public string $code;

    protected $guarded = [];
}
