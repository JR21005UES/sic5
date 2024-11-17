<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalogo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'catalogo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'naturaleza_id'
    ];

    // Definir la clave primaria
    protected $primaryKey = 'codigo';

    // Si la clave primaria no es un entero autoincremental, también debes definir esto
    public $incrementing = false;

    // Definir el tipo de clave primaria si no es un entero
    protected $keyType = 'int';
}
