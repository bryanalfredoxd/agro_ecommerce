<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Database\Seeder;

class ProductosVeterinariosSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarse de que existan categorías y marcas necesarias
        $categoriaVet = Categoria::firstOrCreate(
            ['nombre' => 'Productos Veterinarios'],
            ['categoria_padre_id' => null]
        );

        $marcaVet = Marca::firstOrCreate(
            ['nombre' => 'Veterinaria XYZ'],
            ['activo' => true]
        );

        // Productos controlados veterinarios
        $productosVeterinarios = [
            [
                'categoria_id' => $categoriaVet->id,
                'marca_id' => $marcaVet->id,
                'proveedor_defecto_id' => null,
                'nombre' => 'Ivermectina Inyectable 1%',
                'descripcion' => 'Antiparasitario inyectable para bovinos, ovinos y caprinos. Tratamiento contra parásitos internos y externos.',
                'sku' => 'IVM-001-100ML',
                'codigo_barras' => '7501234567890',
                'costo_promedio_usd' => 12.50,
                'precio_venta_usd' => 15.50,
                'precio_oferta_usd' => null,
                'unidad_medida' => 'ml',
                'contenido_neto' => 100.000,
                'unidad_contenido' => 'ml',
                'es_controlado' => true,
                'atributos_json' => json_encode([
                    'ingrediente_activo' => 'Ivermectina',
                    'concentracion' => '1%',
                    'tiempo_retiro' => '28 días',
                    'toxicidad' => 'Banda Azul',
                    'especies' => ['bovino', 'ovino', 'caprino']
                ]),
                'stock_total' => 50.000,
                'stock_minimo_alerta' => 10.000,
                'venta_minima' => 10.000,
            ],
            [
                'categoria_id' => $categoriaVet->id,
                'marca_id' => $marcaVet->id,
                'proveedor_defecto_id' => null,
                'nombre' => 'Oxitetraciclina 20% Inyectable',
                'descripcion' => 'Antibiótico inyectable de amplio espectro para tratamiento de infecciones bacterianas.',
                'sku' => 'OXI-020-250ML',
                'codigo_barras' => '7501234567891',
                'costo_promedio_usd' => 18.75,
                'precio_venta_usd' => 22.00,
                'precio_oferta_usd' => null,
                'unidad_medida' => 'ml',
                'contenido_neto' => 250.000,
                'unidad_contenido' => 'ml',
                'es_controlado' => true,
                'atributos_json' => json_encode([
                    'ingrediente_activo' => 'Oxitetraciclina',
                    'concentracion' => '20%',
                    'tiempo_retiro' => '21 días',
                    'toxicidad' => 'Banda Roja',
                    'especies' => ['bovino', 'porcino', 'aviar']
                ]),
                'stock_total' => 30.000,
                'stock_minimo_alerta' => 5.000,
                'venta_minima' => 25.000,
            ],
            [
                'categoria_id' => $categoriaVet->id,
                'marca_id' => $marcaVet->id,
                'proveedor_defecto_id' => null,
                'nombre' => 'Vacuna contra Fiebre Aftosa',
                'descripcion' => 'Vacuna inactivada contra fiebre aftosa para bovinos. Requiere receta veterinaria obligatoria.',
                'sku' => 'VAC-AFT-50DOS',
                'codigo_barras' => '7501234567892',
                'costo_promedio_usd' => 45.00,
                'precio_venta_usd' => 55.00,
                'precio_oferta_usd' => null,
                'unidad_medida' => 'dosis',
                'contenido_neto' => 50.000,
                'unidad_contenido' => 'unidad',
                'es_controlado' => true,
                'atributos_json' => json_encode([
                    'tipo_vacuna' => 'Inactivada',
                    'enfermedad' => 'Fiebre Aftosa',
                    'tiempo_retiro' => '21 días',
                    'requiere_refrigeracion' => true,
                    'especies' => ['bovino']
                ]),
                'stock_total' => 100.000,
                'stock_minimo_alerta' => 20.000,
                'venta_minima' => 10.000,
            ],
            [
                'categoria_id' => $categoriaVet->id,
                'marca_id' => $marcaVet->id,
                'proveedor_defecto_id' => null,
                'nombre' => 'Albendazol Oral 10%',
                'descripcion' => 'Anthelmíntico oral de amplio espectro para el control de parásitos gastrointestinales.',
                'sku' => 'ALB-010-500G',
                'codigo_barras' => '7501234567893',
                'costo_promedio_usd' => 8.50,
                'precio_venta_usd' => 12.00,
                'precio_oferta_usd' => null,
                'unidad_medida' => 'g',
                'contenido_neto' => 500.000,
                'unidad_contenido' => 'g',
                'es_controlado' => true,
                'atributos_json' => json_encode([
                    'ingrediente_activo' => 'Albendazol',
                    'concentracion' => '10%',
                    'tiempo_retiro' => '14 días',
                    'toxicidad' => 'Banda Azul',
                    'via_administracion' => 'Oral',
                    'especies' => ['bovino', 'ovino', 'caprino', 'porcino']
                ]),
                'stock_total' => 75.000,
                'stock_minimo_alerta' => 15.000,
                'venta_minima' => 50.000,
            ],
            [
                'categoria_id' => $categoriaVet->id,
                'marca_id' => $marcaVet->id,
                'proveedor_defecto_id' => null,
                'nombre' => 'Vitaminas ADE Inyectable',
                'descripcion' => 'Complejo vitamínico inyectable con vitaminas A, D3 y E para suplementación nutricional.',
                'sku' => 'VIT-ADE-100ML',
                'codigo_barras' => '7501234567894',
                'costo_promedio_usd' => 6.25,
                'precio_venta_usd' => 9.50,
                'precio_oferta_usd' => null,
                'unidad_medida' => 'ml',
                'contenido_neto' => 100.000,
                'unidad_contenido' => 'ml',
                'es_controlado' => true,
                'atributos_json' => json_encode([
                    'vitaminas' => ['A', 'D3', 'E'],
                    'tiempo_retiro' => '0 días',
                    'toxicidad' => 'Baja',
                    'especies' => ['bovino', 'ovino', 'caprino', 'equino']
                ]),
                'stock_total' => 40.000,
                'stock_minimo_alerta' => 8.000,
                'venta_minima' => 5.000,
            ],
        ];

        foreach ($productosVeterinarios as $productoData) {
            Producto::firstOrCreate(
                ['sku' => $productoData['sku']],
                $productoData
            );
        }

        $this->command->info('Productos veterinarios controlados creados exitosamente.');
    }
}