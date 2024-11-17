<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partida extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table='partida';
    protected $fillable =[
        'num_de_partida' ,
        'fecha' ,
        'concepto'
    ];
}
