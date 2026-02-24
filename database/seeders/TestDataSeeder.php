<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pedido;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios de prueba
        $usuarios = [
            [
                'rol_id' => 3, // Cliente
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'juan.perez@email.com',
                'password_hash' => Hash::make('password123'),
                'telefono' => '+584121234567',
                'documento_identidad' => 'V-12345678',
                'tipo_cliente' => 'natural',
                'activo' => true,
            ],
            [
                'rol_id' => 3, // Cliente
                'nombre' => 'María',
                'apellido' => 'García',
                'email' => 'maria.garcia@email.com',
                'password_hash' => Hash::make('password123'),
                'telefono' => '+584141234567',
                'documento_identidad' => 'V-87654321',
                'tipo_cliente' => 'natural',
                'activo' => true,
            ],
            [
                'rol_id' => 3, // Cliente
                'nombre' => 'Finca',
                'apellido' => 'Los Andes',
                'email' => 'finca.andres@email.com',
                'password_hash' => Hash::make('password123'),
                'telefono' => '+584161234567',
                'documento_identidad' => 'J-123456789',
                'tipo_cliente' => 'finca_productor',
                'activo' => true,
            ],
        ];

        foreach ($usuarios as $usuarioData) {
            User::firstOrCreate(
                ['email' => $usuarioData['email']],
                $usuarioData
            );
        }

        // Crear pedidos de prueba
        $clientes = User::where('rol_id', 3)->get();

        foreach ($clientes as $cliente) {
            // Verificar si ya existe un pedido para este cliente
            $pedidoExistente = Pedido::where('usuario_id', $cliente->id)->first();
            
            if (!$pedidoExistente) {
                Pedido::create([
                    'usuario_id' => $cliente->id,
                    'canal_venta' => 'web',
                    'tasa_cambio_id' => 1, // Asumiendo que existe una tasa de cambio
                    'estado' => 'pendiente',
                    'subtotal_usd' => 100.00,
                    'costo_delivery_usd' => 5.00,
                    'total_usd' => 116.00,
                    'total_ves_calculado' => 116.00 * 36.50, // Ejemplo con tasa de cambio
                    'direccion_texto' => 'Dirección de prueba',
                    'instrucciones_entrega' => 'Pedido de prueba para sistema de recetas veterinarias',
                ]);
            }
        }

        $this->command->info('Datos de prueba creados exitosamente.');
    }
}