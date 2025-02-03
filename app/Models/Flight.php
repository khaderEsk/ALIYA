<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;
    protected $table = "flights";
    protected $fillable = [
        'targetPoint',
        'statingPoint',
        'numberPassengers',
        'startingTime',
        'endingTime',
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function startingPointGovernorate()
    {
        return $this->belongsTo(Governorate::class, 'statingPoint');
    }

    public function targetPointGovernorate()
    {
        return $this->belongsTo(Governorate::class, 'targetPoint');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function Passenger()
    {
        return $this->hasMany(Passenger::class, 'flight_id');
    }
}
