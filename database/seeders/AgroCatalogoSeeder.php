<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\InventarioLote;
use App\Models\RecetaVeterinaria;
use App\Models\User;

class AgroCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Proveedores Agropecuarios
        $proveedorAgripac = Proveedor::updateOrCreate(
            ['rif' => 'J-00123456-7'],
            [
                'razon_social' => 'Agripac de Venezuela C.A.',
                'telefono' => '+58 273-5321122',
                'persona_contacto' => 'Ing. Agr. Carlos Mendoza',
                'activo' => 1
            ]
        );

        $proveedorPolar = Proveedor::updateOrCreate(
            ['rif' => 'J-00041235-9'],
            [
                'razon_social' => 'Alimentos Polar División Agropecuaria',
                'telefono' => '+58 212-2023111',
                'persona_contacto' => 'Lic. Roberto Páez',
                'activo' => 1
            ]
        );

        $proveedorZoetis = Proveedor::updateOrCreate(
            ['rif' => 'J-30459812-3'],
            [
                'razon_social' => 'Laboratorios Zoetis Salud Animal C.A.',
                'telefono' => '+58 241-8715560',
                'persona_contacto' => 'Dr. Manuel Valero M.V.',
                'activo' => 1
            ]
        );

        $proveedorSemillas = Proveedor::updateOrCreate(
            ['rif' => 'J-40192837-1'],
            [
                'razon_social' => 'Semillas del Llano & Pastos C.A.',
                'telefono' => '+58 255-6214490',
                'persona_contacto' => 'Ing. José Peña',
                'activo' => 1
            ]
        );

        // 2. Marcas Agropecuarias
        $marcasData = [
            ['nombre' => 'Agripac', 'pais_origen' => 'Venezuela'],
            ['nombre' => 'Purina', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Zoetis Salud Animal', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Bayer CropScience', 'pais_origen' => 'Alemania'],
            ['nombre' => 'Monaca Agro', 'pais_origen' => 'Venezuela'],
            ['nombre' => 'Semillas del Llano', 'pais_origen' => 'Venezuela'],
            ['nombre' => 'Royal Cóndor', 'pais_origen' => 'Colombia'],
            ['nombre' => 'Fercon', 'pais_origen' => 'Colombia'],
        ];

        $marcas = [];
        foreach ($marcasData as $m) {
            $marcas[$m['nombre']] = Marca::updateOrCreate(
                ['nombre' => $m['nombre']],
                ['pais_origen' => $m['pais_origen'], 'activo' => 1]
            );
        }

        // 3. Categorías Agropecuarias
        $categoriasData = [
            'Fertilizantes y Nutrición Vegetal',
            'Alimentos Balanceados y Forrajes',
            'Fármacos y Salud Veterinaria',
            'Semillas Certificadas y Pastos',
            'Agroquímicos y Protección de Cultivos',
            'Herramientas y Equipos de Riego'
        ];

        $categorias = [];
        foreach ($categoriasData as $nombreCat) {
            $categorias[$nombreCat] = Categoria::firstOrCreate(['nombre' => $nombreCat]);
        }

        // 4. Catálogo de Productos Agropecuarios
        $productosData = [
            [
                'sku' => 'FERT-UREA-50KG',
                'codigo_barras' => '759100010001',
                'nombre' => 'Urea Agrícola Perlada 46% Nitrógeno (50 Kg)',
                'descripcion' => 'Fertilizante nitrogenado de alta asimilación para aplicación edáfica en cultivos de maíz, caña, arroz y pastos ganaderos.',
                'categoria_id' => $categorias['Fertilizantes y Nutrición Vegetal']->id,
                'marca_id' => $marcas['Agripac']->id,
                'proveedor_defecto_id' => $proveedorAgripac->id,
                'costo_promedio_usd' => 24.00,
                'precio_venta_usd' => 32.00,
                'unidad_medida' => 'saco',
                'contenido_neto' => 50.000,
                'unidad_contenido' => 'kg',
                'es_controlado' => false,
                'destacado' => true,
                'stock_total' => 150.000,
                'stock_minimo_alerta' => 20,
                'atributos_json' => json_encode([
                    'Nitrógeno Total' => '46%',
                    'Presentación' => 'Perlada Blanca',
                    'Solubilidad' => 'Alta en agua',
                    'Dosis Sugerida' => '100 a 150 kg por hectárea'
                ]),
                'lotes' => [
                    [
                        'numero_lote' => 'LT-UREA-2026-A',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+365 days')),
                        'cantidad' => 150.000,
                        'costo' => 24.00,
                        'almacen' => 'Galpón 1 - Racks Fertilizantes'
                    ]
                ]
            ],
            [
                'sku' => 'FERT-NPK-122412',
                'codigo_barras' => '759100010002',
                'nombre' => 'Fertilizante Compuesto NPK 12-24-12 Triple Arranque (50 Kg)',
                'descripcion' => 'Fórmula completa ideal para el establecimiento de cultivos con alto requerimiento de fósforo en la fase inicial de enraizamiento.',
                'categoria_id' => $categorias['Fertilizantes y Nutrición Vegetal']->id,
                'marca_id' => $marcas['Agripac']->id,
                'proveedor_defecto_id' => $proveedorAgripac->id,
                'costo_promedio_usd' => 28.50,
                'precio_venta_usd' => 38.00,
                'unidad_medida' => 'saco',
                'contenido_neto' => 50.000,
                'unidad_contenido' => 'kg',
                'es_controlado' => false,
                'destacado' => false,
                'stock_total' => 85.000,
                'stock_minimo_alerta' => 15,
                'atributos_json' => json_encode([
                    'Nitrógeno' => '12%',
                    'Fósforo (P2O5)' => '24%',
                    'Potasio (K2O)' => '12%'
                ]),
                'lotes' => [
                    [
                        'numero_lote' => 'LT-NPK-2026-03',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+500 days')),
                        'cantidad' => 85.000,
                        'costo' => 28.50,
                        'almacen' => 'Galpón 1 - Racks Fertilizantes'
                    ]
                ]
            ],
            [
                'sku' => 'ALIM-LECHE-18P',
                'codigo_barras' => '759100020001',
                'nombre' => 'Alimento Concentrado Bovino Lechero 18% Proteína (40 Kg)',
                'descripcion' => 'Alimento balanceado extruido para vacas en ordeño de alta producción. Formulado con energía y aminoácidos digestibles.',
                'categoria_id' => $categorias['Alimentos Balanceados y Forrajes']->id,
                'marca_id' => $marcas['Purina']->id,
                'proveedor_defecto_id' => $proveedorPolar->id,
                'costo_promedio_usd' => 17.00,
                'precio_venta_usd' => 23.50,
                'unidad_medida' => 'saco',
                'contenido_neto' => 40.000,
                'unidad_contenido' => 'kg',
                'es_controlado' => false,
                'destacado' => true,
                'stock_total' => 200.000,
                'stock_minimo_alerta' => 30,
                'atributos_json' => json_encode([
                    'Proteína Cruda Mínima' => '18%',
                    'Grasa Mínima' => '3.5%',
                    'Fibra Máxima' => '8.0%',
                    'Energía Neta de Lactancia' => '1.65 Mcal/kg'
                ]),
                'lotes' => [
                    [
                        'numero_lote' => 'LT-PURINA-LECHE-26',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+120 days')),
                        'cantidad' => 200.000,
                        'costo' => 17.00,
                        'almacen' => 'Galpón 2 - Piensos y Concentrados'
                    ]
                ]
            ],
            [
                'sku' => 'ALIM-SAL-MIN-8P',
                'codigo_barras' => '759100020002',
                'nombre' => 'Sal Mineralizada Bovino Ganadero 8% Fósforo (25 Kg)',
                'descripcion' => 'Suplemento mineral micro y macroelementos formulado especialmente para suelos de sabana y llanos occidentales.',
                'categoria_id' => $categorias['Alimentos Balanceados y Forrajes']->id,
                'marca_id' => $marcas['Monaca Agro']->id,
                'proveedor_defecto_id' => $proveedorPolar->id,
                'costo_promedio_usd' => 14.00,
                'precio_venta_usd' => 19.00,
                'unidad_medida' => 'saco',
                'contenido_neto' => 25.000,
                'unidad_contenido' => 'kg',
                'es_controlado' => false,
                'destacado' => false,
                'stock_total' => 120.000,
                'stock_minimo_alerta' => 25,
                'atributos_json' => json_encode([
                    'Fósforo' => '8.0%',
                    'Calcio' => '12.5%',
                    'Magnesio' => '1.5%',
                    'Zinc' => '3500 ppm'
                ]),
                'lotes' => [
                    [
                        'numero_lote' => 'LT-SAL-MIN-01',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+400 days')),
                        'cantidad' => 120.000,
                        'costo' => 14.00,
                        'almacen' => 'Galpón 2 - Piensos y Concentrados'
                    ]
                ]
            ],
            [
                'sku' => 'VET-IVERM-500ML',
                'codigo_barras' => '759100030001',
                'nombre' => 'Ivermectina 1% Endectocida Inyectable (Frasco 500 ml)',
                'descripcion' => 'Solución inyectable antiparasitaria de amplio espectro contra nemátodos gastrointestinales y ectoparásitos (garrapatas, tórsalos y piojos). Requiere indicación veterinaria.',
                'categoria_id' => $categorias['Fármacos y Salud Veterinaria']->id,
                'marca_id' => $marcas['Zoetis Salud Animal']->id,
                'proveedor_defecto_id' => $proveedorZoetis->id,
                'costo_promedio_usd' => 12.50,
                'precio_venta_usd' => 18.50,
                'unidad_medida' => 'unidad',
                'contenido_neto' => 500.000,
                'unidad_contenido' => 'ml',
                'es_controlado' => true, // CONTROLADO POR RECETA MÉDICA
                'destacado' => true,
                'stock_total' => 60.000,
                'stock_minimo_alerta' => 10,
                'atributos_json' => json_encode([
                    'Principio Activo' => 'Ivermectina 10 mg/ml',
                    'Vía de Administración' => 'Subcutánea en la tabla del cuello',
                    'Dosis' => '1 ml por cada 50 kg de peso vivo',
                    'Período de Retiro Carne' => '35 días',
                    'Condición de Venta' => 'Bajo Récipe Médico Veterinario'
                ]),
                'lotes' => [
                    [
                        // ¡Lote próximo a vencer (18 días) para probar vista view_productos_proximos_vencer!
                        'numero_lote' => 'LT-IVERM-VENC-PROX',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+18 days')),
                        'cantidad' => 20.000,
                        'costo' => 12.50,
                        'almacen' => 'Farmacia Veterinaria - Estante 2'
                    ],
                    [
                        // Lote con vencimiento regular
                        'numero_lote' => 'LT-IVERM-2027-OK',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+450 days')),
                        'cantidad' => 40.000,
                        'costo' => 12.50,
                        'almacen' => 'Farmacia Veterinaria - Estante 2'
                    ]
                ]
            ],
            [
                'sku' => 'VET-OXITET-250ML',
                'codigo_barras' => '759100030002',
                'nombre' => 'Oxitetraciclina L.A. 200 mg/ml Antibiótico Larga Acción (250 ml)',
                'descripcion' => 'Antibiótico inyectable de amplio espectro con acción prolongada de hasta 72 horas para neumonías, anaplasmosis, pododermatitis y mastitis.',
                'categoria_id' => $categorias['Fármacos y Salud Veterinaria']->id,
                'marca_id' => $marcas['Zoetis Salud Animal']->id,
                'proveedor_defecto_id' => $proveedorZoetis->id,
                'costo_promedio_usd' => 15.00,
                'precio_venta_usd' => 22.00,
                'unidad_medida' => 'unidad',
                'contenido_neto' => 250.000,
                'unidad_contenido' => 'ml',
                'es_controlado' => true, // CONTROLADO POR RECETA MÉDICA
                'destacado' => false,
                'stock_total' => 45.000,
                'stock_minimo_alerta' => 8,
                'atributos_json' => json_encode([
                    'Principio Activo' => 'Oxitetraciclina Base 200 mg/ml',
                    'Vía de Administración' => 'Intramuscular profunda',
                    'Dosis' => '1 ml por cada 10 kg de peso vivo',
                    'Período de Retiro Leche' => '7 días'
                ]),
                'lotes' => [
                    [
                        // Lote con Alerta Media (55 días) para probar vista view_productos_proximos_vencer
                        'numero_lote' => 'LT-OXITET-MEDIA-ALERTA',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+55 days')),
                        'cantidad' => 15.000,
                        'costo' => 15.00,
                        'almacen' => 'Farmacia Veterinaria - Estante 1'
                    ],
                    [
                        'numero_lote' => 'LT-OXITET-2027',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+380 days')),
                        'cantidad' => 30.000,
                        'costo' => 15.00,
                        'almacen' => 'Farmacia Veterinaria - Estante 1'
                    ]
                ]
            ],
            [
                'sku' => 'SEM-MAIZ-H2004',
                'codigo_barras' => '759100040001',
                'nombre' => 'Semilla de Maíz Blanco Híbrido H-2004 Certificada (Saco 60.000 Semillas)',
                'descripcion' => 'Híbrido de alta productividad adaptado a las condiciones de sabana y suelos llaneros. Excelente tolerancia a sequía y volcamiento.',
                'categoria_id' => $categorias['Semillas Certificadas y Pastos']->id,
                'marca_id' => $marcas['Semillas del Llano']->id,
                'proveedor_defecto_id' => $proveedorSemillas->id,
                'costo_promedio_usd' => 95.00,
                'precio_venta_usd' => 130.00,
                'unidad_medida' => 'saco',
                'contenido_neto' => 60000.000,
                'unidad_contenido' => 'unidad',
                'es_controlado' => false,
                'destacado' => true,
                'stock_total' => 40.000,
                'stock_minimo_alerta' => 5,
                'atributos_json' => json_encode([
                    'Población Recomendada' => '60.000 plantas/ha',
                    'Germinación Mínima' => '92%',
                    'Pureza Física' => '99%',
                    'Tratamiento' => 'Curasemilla Fungicida e Insecticida'
                ]),
                'lotes' => [
                    [
                        'numero_lote' => 'LT-SEM-MAIZ-26-Z',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+240 days')),
                        'cantidad' => 40.000,
                        'costo' => 95.00,
                        'almacen' => 'Cuarto Climatizado Semillas'
                    ]
                ]
            ],
            [
                'sku' => 'AGRO-GLIFO-4L',
                'codigo_barras' => '759100050001',
                'nombre' => 'Herbicida Glifosato Sistémico 480 SL Concentrado (Galón 4 Litros)',
                'descripcion' => 'Herbicida post-emergente no selectivo para el control total de malezas gramíneas y de hoja ancha antes de la siembra cero labranza.',
                'categoria_id' => $categorias['Agroquímicos y Protección de Cultivos']->id,
                'marca_id' => $marcas['Bayer CropScience']->id,
                'proveedor_defecto_id' => $proveedorAgripac->id,
                'costo_promedio_usd' => 18.00,
                'precio_venta_usd' => 26.00,
                'unidad_medida' => 'galon',
                'contenido_neto' => 4.000,
                'unidad_contenido' => 'l',
                'es_controlado' => false,
                'destacado' => false,
                'stock_total' => 90.000,
                'stock_minimo_alerta' => 15,
                'atributos_json' => json_encode([
                    'Concentración' => '480 g/L de Sal Isopropilamina',
                    'Acción' => 'Sistémica vía foliar',
                    'Categoría Toxicológica' => 'IV (Precaución - Franja Verde)'
                ]),
                'lotes' => [
                    [
                        'numero_lote' => 'LT-BAYER-GLIFO-26',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+600 days')),
                        'cantidad' => 90.000,
                        'costo' => 18.00,
                        'almacen' => 'Galpón 3 - Depósito Químicos'
                    ]
                ]
            ],
            [
                'sku' => 'EQUIP-FUMIG-20L',
                'codigo_barras' => '759100060001',
                'nombre' => 'Aspersora Fumigadora de Espalda Manual Royal Cóndor (20 Litros)',
                'descripcion' => 'Bomba aspersora ergonómica de émbolo metálico y lanza de latón para aplicaciones fitosanitarias de alta precisión.',
                'categoria_id' => $categorias['Herramientas y Equipos de Riego']->id,
                'marca_id' => $marcas['Royal Cóndor']->id,
                'proveedor_defecto_id' => $proveedorAgripac->id,
                'costo_promedio_usd' => 42.00,
                'precio_venta_usd' => 58.00,
                'unidad_medida' => 'unidad',
                'contenido_neto' => 20.000,
                'unidad_contenido' => 'l',
                'es_controlado' => false,
                'destacado' => true,
                'stock_total' => 25.000,
                'stock_minimo_alerta' => 4,
                'atributos_json' => json_encode([
                    'Capacidad Tanque' => '20 Litros',
                    'Material Tanque' => 'Polietileno de alta densidad',
                    'Boquillas Incluidas' => 'Cono hueco, abanico plano y doble',
                    'Presión de Trabajo' => '40 a 60 PSI'
                ]),
                'lotes' => [
                    [
                        'numero_lote' => 'LT-RC-BOMBAS-26',
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+1200 days')),
                        'cantidad' => 25.000,
                        'costo' => 42.00,
                        'almacen' => 'Galpón 4 - Maquinaria y Equipos'
                    ]
                ]
            ]
        ];

        foreach ($productosData as $pData) {
            $lotes = $pData['lotes'] ?? [];
            unset($pData['lotes']);

            $producto = Producto::updateOrCreate(
                ['sku' => $pData['sku']],
                $pData
            );

            // Crear los lotes correspondientes
            foreach ($lotes as $lote) {
                InventarioLote::updateOrCreate(
                    [
                        'producto_id' => $producto->id,
                        'numero_lote' => $lote['numero_lote']
                    ],
                    [
                        'proveedor_id' => $producto->proveedor_defecto_id,
                        'fecha_vencimiento' => $lote['fecha_vencimiento'],
                        'cantidad_inicial' => $lote['cantidad'],
                        'cantidad_restante' => $lote['cantidad'],
                        'costo_unitario_usd' => $lote['costo'],
                        'ubicacion_almacen' => $lote['almacen'],
                        'activo' => 1
                    ]
                );
            }
        }

        // 5. Crear una Receta Veterinaria demostrativa
        $cliente = User::where('email', 'migdelismarquez394@gmail.com')->first();
        if ($cliente) {
            $productoIvermectina = Producto::where('sku', 'VET-IVERM-500ML')->first();
            $productoOxitetraciclina = Producto::where('sku', 'VET-OXITET-250ML')->first();

            // Pedido base existente (ej. Pedido ID 1)
            $pedidoId = DB::table('pedidos')->value('id');

            if ($pedidoId && $productoIvermectina) {
                $recetaId = DB::table('recetas_veterinarias')->insertGetId([
                    'pedido_id' => $pedidoId,
                    'cliente_usuario_id' => $cliente->id,
                    'veterinario_nombre' => 'Dr. Francisco Rivas',
                    'veterinario_matricula' => 'COLVET-14280',
                    'cliente_animal_tipo' => 'Bovinos Mautas Mestizas',
                    'cliente_animal_cantidad' => 45,
                    'fecha_prescription' => now()->toDateString(),
                    'fecha_vencimiento_receta' => now()->addDays(30)->toDateString(),
                    'archivo_url' => 'recetas/demo_receta_veterinaria_firmada.pdf',
                    'estado' => 'aprobada',
                    'observaciones' => 'Tratamiento profiláctico post-destete y control de anaplasmosis.',
                    'usuario_revisa_id' => 1,
                    'fecha_revision' => now(),
                    'creado_at' => now()
                ]);

                DB::table('receta_productos')->insert([
                    [
                        'receta_id' => $recetaId,
                        'producto_id' => $productoIvermectina->id,
                        'cantidad_prescrita' => 2.000,
                        'dosis_instrucciones' => '1 ml por cada 50 kg SC en la tabla del cuello dosis única.',
                        'autorizado' => 1
                    ],
                    [
                        'receta_id' => $recetaId,
                        'producto_id' => $productoOxitetraciclina->id,
                        'cantidad_prescrita' => 3.000,
                        'dosis_instrucciones' => '1 ml por cada 10 kg IM profunda cada 72 horas por 2 dosis.',
                        'autorizado' => 1
                    ]
                ]);
            }
        }
    }
}
