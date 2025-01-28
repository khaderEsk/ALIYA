<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $table = "governorates";
    protected $fillable = [
        'name',
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function startingFlights()
    {
        return $this->hasMany(Flight::class, 'statingPoint');
    }

    public function targetFlights()
    {
        return $this->hasMany(Flight::class, 'targetPoint');
    }
}
