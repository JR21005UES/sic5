<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    use HasFactory;
    protected $table='partida';
    protected $fillable =[
        'num_de_partida' ,
        'fecha' ,
        'concepto'
    ];
}
