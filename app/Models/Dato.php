<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dato extends Model
{
    use HasFactory;
    protected $table='dato';
    protected $fillable =[
        'id_catalogo',
        'id_partida',
        'debe',
        'haber'
    ];
}
