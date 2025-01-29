<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;
    protected $table = "passenger";
    protected $fillable = [
        'numberPassenger',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    public function flights()
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }
}
