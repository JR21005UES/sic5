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
        'es_diario',//agregamos campo para comprobar si es cierre 
        'debe',
        'haber'
    ];
    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class, 'id_catalogo', 'codigo');
    }
    //lo mismo pero para la partida
    public function partida()
    {
        return $this->belongsTo(Partida::class, 'id_partida', 'id');
    }
}
