<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioSemanal extends Model
{
    protected $table = 'horarios_semanales';
    
    protected $fillable = [
        'dia_semana',
        'es_laborable',
        'hora_apertura',
        'hora_cierre'
    ];
    
    protected $casts = [
        'es_laborable' => 'boolean',
        'dia_semana' => 'integer'
    ];
    
    // Días de la semana en español
    private static $diasSemana = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo'
    ];
    
    // Obtener el horario del día actual
    public static function obtenerHorarioHoy()
    {
        $diaActual = date('N'); // 1 (Lunes) hasta 7 (Domingo)
        
        return self::where('dia_semana', $diaActual)->first();
    }
    
    // Obtener horario formateado para hoy
    public static function obtenerHorarioHoyFormateado()
    {
        $horarioHoy = self::obtenerHorarioHoy();
        
        if (!$horarioHoy) {
            return 'Cerrado hoy';
        }
        
        if (!$horarioHoy->es_laborable) {
            return 'Cerrado hoy';
        }
        
        if (!$horarioHoy->hora_apertura || !$horarioHoy->hora_cierre) {
            return 'Horario especial';
        }
        
        // Formatear horas en formato 12h (8am - 5pm)
        $horaApertura = date('ga', strtotime($horarioHoy->hora_apertura));
        $horaCierre = date('ga', strtotime($horarioHoy->hora_cierre));
        
        return $horaApertura . ' - ' . $horaCierre;
    }
    
    // Obtener nombre del día en español
    public function getNombreDiaAttribute()
    {
        return self::$diasSemana[$this->dia_semana] ?? 'Día ' . $this->dia_semana;
    }
    
    // Verificar si está abierto en este momento
    public static function estaAbiertoAhora()
    {
        $horarioHoy = self::obtenerHorarioHoy();
        
        if (!$horarioHoy || !$horarioHoy->es_laborable) {
            return false;
        }
        
        $horaActual = date('H:i:s');
        $horaApertura = $horarioHoy->hora_apertura;
        $horaCierre = $horarioHoy->hora_cierre;
        
        if (!$horaApertura || !$horaCierre) {
            return false;
        }
        
        return ($horaActual >= $horaApertura && $horaActual <= $horaCierre);
    }
}