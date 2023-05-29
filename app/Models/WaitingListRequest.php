<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class WaitingListRequest extends Model
{
    public string $email;
    public string $phone;
    public string $country_code;

    protected $guarded = [];

    use HasFactory;
}
