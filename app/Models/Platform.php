<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    public string $name;
    public array $details;

    protected $hidden = ["updated_at", "created_at"];

    protected $guarded = [];
}
