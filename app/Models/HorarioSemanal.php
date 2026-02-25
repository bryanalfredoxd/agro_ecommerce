<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioSemanal extends Model
{
    protected $table = 'horarios_semanales';

    public $timestamps = false;
    
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
    
    // Obtener estado del horario actual con mensaje
    public static function obtenerEstadoHorarioHoy()
    {
        $horarioHoy = self::obtenerHorarioHoy();
        
        if (!$horarioHoy) {
            return [
                'estado' => 'sin_horario',
                'mensaje' => 'Sin horario definido',
                'hora_apertura' => null,
                'hora_cierre' => null,
                'horario_completo' => null,
                'mostrar' => 'Sin horario definido'
            ];
        }
        
        // Si no es día laborable
        if (!$horarioHoy->es_laborable) {
            return [
                'estado' => 'no_laborable',
                'mensaje' => 'Día no laborable',
                'hora_apertura' => null,
                'hora_cierre' => null,
                'horario_completo' => null,
                'mostrar' => 'Día no laborable'
            ];
        }
        
        // Si no tiene horarios definidos
        if (!$horarioHoy->hora_apertura || !$horarioHoy->hora_cierre) {
            return [
                'estado' => 'sin_horario_dia',
                'mensaje' => 'Horario especial',
                'hora_apertura' => null,
                'hora_cierre' => null,
                'horario_completo' => 'Horario especial',
                'mostrar' => 'Horario especial'
            ];
        }
        
        // Obtener la hora actual en segundos desde medianoche
        $horaActual = self::horaActualEnSegundos();
        $horaApertura = self::horaASegundos($horarioHoy->hora_apertura);
        $horaCierre = self::horaASegundos($horarioHoy->hora_cierre);
        
        // Formatear horas para mostrar
        $horaAperturaFormato = date('ga', strtotime($horarioHoy->hora_apertura));
        $horaCierreFormato = date('ga', strtotime($horarioHoy->hora_cierre));
        $horarioCompleto = $horaAperturaFormato . ' - ' . $horaCierreFormato;
        
        // Verificar estado actual
        if ($horaActual < $horaApertura) {
            // Antes del horario de apertura
            return [
                'estado' => 'antes_horario',
                'mensaje' => 'Abrimos a las ' . $horaAperturaFormato,
                'hora_apertura' => $horarioHoy->hora_apertura,
                'hora_cierre' => $horarioHoy->hora_cierre,
                'horario_completo' => $horarioCompleto,
                'hora_apertura_formato' => $horaAperturaFormato,
                'mostrar' => 'Abrimos a las ' . $horaAperturaFormato
            ];
        } elseif ($horaActual > $horaCierre) {
            // Después del horario de cierre
            return [
                'estado' => 'despues_horario',
                'mensaje' => 'Ya cerramos',
                'hora_apertura' => $horarioHoy->hora_apertura,
                'hora_cierre' => $horarioHoy->hora_cierre,
                'horario_completo' => $horarioCompleto,
                'mostrar' => 'Ya cerramos'
            ];
        } else {
            // Dentro del horario laboral
            return [
                'estado' => 'abierto',
                'mensaje' => 'Abierto',
                'hora_apertura' => $horarioHoy->hora_apertura,
                'hora_cierre' => $horarioHoy->hora_cierre,
                'horario_completo' => $horarioCompleto,
                'mostrar' => $horarioCompleto
            ];
        }
    }
    
    // Convertir hora HH:MM:SS a segundos desde medianoche
    private static function horaASegundos($hora)
    {
        if (!$hora) return 0;
        
        $partes = explode(':', $hora);
        $horas = (int)$partes[0];
        $minutos = (int)($partes[1] ?? 0);
        $segundos = (int)($partes[2] ?? 0);
        
        return ($horas * 3600) + ($minutos * 60) + $segundos;
    }
    
    // Obtener hora actual en segundos desde medianoche
    private static function horaActualEnSegundos()
    {
        $horaActual = date('H:i:s');
        return self::horaASegundos($horaActual);
    }
    
    // Método simplificado para obtener solo el mensaje a mostrar
    public static function obtenerHorarioHoyFormateado()
    {
        $estado = self::obtenerEstadoHorarioHoy();
        return $estado['mostrar'];
    }
    
    // Verificar si está abierto en este momento (para otros usos)
    public static function estaAbiertoAhora()
    {
        $estado = self::obtenerEstadoHorarioHoy();
        return $estado['estado'] === 'abierto';
    }
    
    // Obtener nombre del día en español
    public function getNombreDiaAttribute()
    {
        return self::$diasSemana[$this->dia_semana] ?? 'Día ' . $this->dia_semana;
    }
}