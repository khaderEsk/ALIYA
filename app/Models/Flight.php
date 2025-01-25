<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;
    protected $table = "flights";
    protected $fillable = [
        'statingPoint',
        'targetPoint',
        'numberPassengers',
        'startingTime',
        'endingTime',
    ];
    public function startingPointGovernorate()
    {
        return $this->belongsTo(Governorate::class, 'statingPoint');
    }

    public function targetPointGovernorate()
    {
        return $this->belongsTo(Governorate::class, 'targetPoint');
    }
}
