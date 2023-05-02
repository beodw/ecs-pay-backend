<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{ 
    use HasFactory;

    public string $userId;
    public string $currencyId;
    public string $platformId;
    public double $amount;
    public int $recipient;
    public string $status;

    protected $guarded = [];

}
