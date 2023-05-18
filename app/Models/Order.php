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
    public float $amount;
    public $recipient;
    public string $status;
    public $platformDetails;
    public bool $archived = false;

    protected $guarded = [];

}
