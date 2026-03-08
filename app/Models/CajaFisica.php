<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaFisica extends Model
{
    protected $table = 'cajas_fisicas';
    public $timestamps = false;

    protected $fillable = ['nombre', 'activa'];
}