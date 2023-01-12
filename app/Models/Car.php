<?php

namespace App\Models;

use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function Booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class); 
    }
}
