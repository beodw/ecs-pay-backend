<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{ 
    use HasFactory;

    public int $userId;
    public int $currencyId;
    public int $platformId;
    public double $amount;
    public int $recipient;
    public string $status;

    protected $guarded = [];

}
