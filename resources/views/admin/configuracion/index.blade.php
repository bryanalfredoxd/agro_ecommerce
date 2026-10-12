@extends('layouts.admin')

@section('title', 'Configuración de la Tienda - ' . ($config->nombre_empresa ?? 'Agrosuministros Rodríguez'))

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-36 sm:pb-44 max-w-6xl mx-auto w-full">
            
            {{-- Encabezado Principal --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
                <div class="flex items-center gap-3.5">
                    <a href="{{ route('admin.dashboard') }}" class="size-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-400 transition-colors shadow-2xs" title="Volver al Panel">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </a>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-black text-agro-dark flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-[28px]">storefront</span>
                                Configuración de la Tienda
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                Sincronización Global
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                            Administra identidad corporativa, canales de atención, ubicación, precios e impuestos sincronizados en todo el sistema.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 self-start sm:self-auto">
                    <a href="{{ route('admin.horarios.index') }}" class="px-3.5 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-xs font-bold text-gray-700 flex items-center gap-1.5 transition-all shadow-2xs" title="Horarios Semanales">
                        <span class="material-symbols-outlined text-[17px] text-blue-600">schedule</span>
                        <span>Horarios</span>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white flex items-center gap-1.5 transition-all shadow-xs" title="Ver Tienda Web">
                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                        <span>Ver Tienda</span>
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.configuracion.update') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- SECCIÓN 1: IDENTIDAD CORPORATIVA Y DATOS LEGALES --}}
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 sm:p-7">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3.5 mb-5">
                        <h3 class="text-base font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-[22px]">domain</span>
                            Identidad Corporativa y Datos Fiscales
                        </h3>
                        <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            Público & Fiscal
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-5">
                        {{-- Fila 1: Nombre Comercial (8) + RIF (4) --}}
                        <div class="md:col-span-8">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Nombre Comercial de la Empresa <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-gray-400">badge</span>
                                <input type="text" 
                                       name="nombre_empresa" 
                                       value="{{ old('nombre_empresa', $config->nombre_empresa) }}" 
                                       required 
                                       placeholder="Ej: Agrosuministros Rodríguez C.A."
                                       class="w-full h-10.5 pl-11 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-agro-dark text-sm outline-none">
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">Visible en encabezados, pie de página, facturas y correos.</p>
                        </div>
                        
                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Documento / RIF Fiscal <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-gray-400">fingerprint</span>
                                <input type="text" 
                                       name="rif" 
                                       value="{{ old('rif', $config->rif ?? 'J-50493821-4') }}" 
                                       required 
                                       placeholder="Ej: J-50493821-4"
                                       class="w-full h-10.5 pl-11 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-agro-dark text-sm uppercase outline-none">
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">Impreso en comprobantes fiscales y tickets.</p>
                        </div>

                        {{-- Fila 2: Eslogan (8) + IVA % (4) --}}
                        <div class="md:col-span-8">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Eslogan o Subtítulo Comercial
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-gray-400">format_quote</span>
                                <input type="text" 
                                       name="slogan" 
                                       value="{{ old('slogan', $config->slogan ?? 'Insumos & Soluciones para el Campo') }}" 
                                       placeholder="Ej: Insumos & Soluciones para el Campo"
                                       class="w-full h-10.5 pl-11 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-medium text-slate-700 text-sm outline-none">
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">Acompaña a la marca en la web y el pie de página.</p>
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Impuesto IVA (%) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 font-black text-blue-600 text-sm">%</span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       name="iva_porcentaje" 
                                       id="iva_porcentaje" 
                                       value="{{ old('iva_porcentaje', $config->iva_porcentaje) }}" 
                                       required 
                                       class="limit-decimals w-full h-10.5 pl-8 pr-4 rounded-xl bg-blue-50/40 border border-blue-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-black text-blue-700 text-sm outline-none">
                            </div>
                            <p class="text-[11px] text-blue-700 mt-1 ml-0.5 font-semibold">Tasa centralizada para Web y POS.</p>
                        </div>

                        {{-- Fila 3: Descripción Institucional (12) --}}
                        <div class="md:col-span-12">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Descripción Institucional / Reseña para Clientes
                            </label>
                            <textarea name="descripcion_empresa" 
                                      rows="2" 
                                      placeholder="Describe brevemente la actividad de la empresa para tus clientes en el pie de página..."
                                      class="w-full p-3.5 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all text-xs sm:text-sm text-slate-700 font-medium leading-relaxed outline-none">{{ old('descripcion_empresa', $config->descripcion_empresa ?? 'Tu aliado estratégico en el desarrollo agrícola y pecuario. Ofrecemos fertilizantes, semillas certificadas, implementos y farmacología veterinaria con entrega confiable a nivel regional y nacional.') }}</textarea>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">
                                Publicada en la primera columna del pie de página (Footer) de la tienda online.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: CANALES DE CONTACTO Y ATENCIÓN AL CLIENTE (2x2 SIMÉTRICO) --}}
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 sm:p-7">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3.5 mb-5">
                        <h3 class="text-base font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-[22px]">contact_phone</span>
                            Canales de Contacto y Atención al Cliente
                        </h3>
                        <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Atención al Usuario
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                        
                        {{-- 1. WhatsApp Oficial --}}
                        <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-4.5 flex flex-col justify-between hover:border-slate-300 transition-colors">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <label class="text-[11px] font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[17px] text-emerald-600">chat</span>
                                        WhatsApp Oficial
                                    </label>
                                    <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Botón Flotante
                                    </span>
                                </div>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-emerald-600">chat</span>
                                    <input type="text" 
                                           name="whatsapp" 
                                           id="input_whatsapp"
                                           value="{{ old('whatsapp', $config->whatsapp ?? '+58 412-9614615') }}" 
                                           placeholder="+58 412-9614615"
                                           oninput="actualizarPreviewWhatsApp(this.value)"
                                           class="w-full h-10.5 pl-11 pr-4 rounded-xl bg-white border border-gray-200 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 transition-all font-bold text-slate-800 text-sm outline-none">
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between gap-2 text-[11px]">
                                <span class="text-slate-500 font-medium">Enlace directo:</span>
                                <a id="link_preview_wa" 
                                   href="https://wa.me/{{ \App\Models\ConfiguracionTienda::whatsappNumeroLimpio() }}" 
                                   target="_blank" 
                                   class="font-black text-emerald-700 hover:text-emerald-900 hover:underline flex items-center gap-1">
                                    <span>wa.me/<span id="txt_preview_wa">{{ \App\Models\ConfiguracionTienda::whatsappNumeroLimpio() }}</span></span>
                                    <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                                </a>
                            </div>
                        </div>

                        {{-- 2. Teléfono de Llamadas / Central --}}
                        <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-4.5 flex flex-col justify-between hover:border-slate-300 transition-colors">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <label class="text-[11px] font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[17px] text-blue-600">call</span>
                                        Central Telefónica / Llamadas
                                    </label>
                                    <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                        Top-Bar Pública
                                    </span>
                                </div>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-blue-500">call</span>
                                    <input type="text" 
                                           name="telefono" 
                                           value="{{ old('telefono', $config->telefono ?? '+58 412-9614615') }}" 
                                           placeholder="+58 412-9614615"
                                           class="w-full h-10.5 pl-11 pr-4 rounded-xl bg-white border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-slate-800 text-sm outline-none">
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                                <span>Mostrado en la barra superior y comprobantes impresos.</span>
                            </div>
                        </div>

                        {{-- 3. Correo Electrónico Institucional --}}
                        <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-4.5 flex flex-col justify-between hover:border-slate-300 transition-colors">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <label class="text-[11px] font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[17px] text-indigo-600">mail</span>
                                        Correo Electrónico Institucional <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        Remitente Oficial
                                    </span>
                                </div>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-indigo-500">mail</span>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email', $config->email ?? 'fitnessbigworld@gmail.com') }}" 
                                           required 
                                           placeholder="contacto@agrosuministros.com"
                                           class="w-full h-10.5 pl-11 pr-4 rounded-xl bg-white border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-bold text-slate-800 text-sm outline-none">
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                                <span>Visible en pie de página y remitente de facturas.</span>
                            </div>
                        </div>

                        {{-- 4. Horario Laboral de Atención Automático --}}
                        <div class="bg-blue-50/40 rounded-2xl border border-blue-200/80 p-4.5 flex flex-col justify-between hover:border-blue-300 transition-colors">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <label class="text-[11px] font-bold text-blue-900 uppercase tracking-wide flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[17px] text-blue-600">schedule</span>
                                        Horario Laboral de Atención
                                    </label>
                                    <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                        Automático
                                    </span>
                                </div>
                                <div class="h-10.5 px-3.5 rounded-xl bg-white border border-blue-200 flex items-center justify-between">
                                    <p class="text-xs sm:text-sm font-black text-slate-800 flex items-center gap-2 truncate">
                                        <span class="size-2 rounded-full bg-emerald-500 shrink-0 animate-pulse"></span>
                                        <span class="truncate">{{ \App\Models\HorarioSemanal::obtenerResumenSemanal() }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-blue-100/80 flex items-center justify-between gap-2 text-[11px]">
                                <span class="text-blue-800 font-medium">Sincronizado con horarios:</span>
                                <a href="{{ route('admin.horarios.index') }}" class="font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 hover:underline">
                                    <span>Gestionar Días</span>
                                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- SECCIÓN 3: UBICACIÓN Y SEDE FÍSICA (SIMÉTRICA Y COMPACTA) --}}
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 sm:p-7">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3.5 mb-5">
                        <h3 class="text-base font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-rose-600 text-[22px]">location_on</span>
                            Ubicación Física y Coordenadas GPS
                        </h3>
                        <a href="{{ route('admin.zonas_delivery.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                            <span>Mapa Interactivo</span>
                            <span class="material-symbols-outlined text-[16px]">map</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-5">
                        {{-- Dirección Descriptiva (12 columnas) --}}
                        <div class="md:col-span-12">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Dirección Física / Sede Comercial <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-gray-400">home_pin</span>
                                <input type="text" 
                                       name="direccion" 
                                       value="{{ old('direccion', $config->direccion ?? 'Barinitas, Estado Barinas, Venezuela') }}" 
                                       required 
                                       placeholder="Ej: Barinitas, Estado Barinas, Venezuela"
                                       class="w-full h-10.5 pl-11 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-agro-dark text-sm outline-none">
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">Publicada en el pie de página, facturas y tickets emitidos.</p>
                        </div>

                        {{-- Fila 2: Latitud (4) + Longitud (4) + Tarjeta Enlace Mapa (4) --}}
                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Latitud GPS (Sede)
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-gray-400">explore</span>
                                <input type="number" 
                                       step="0.0000001" 
                                       name="latitud" 
                                       value="{{ old('latitud', $config->latitud ?? 8.7622000) }}" 
                                       placeholder="8.7622000"
                                       class="w-full h-10.5 pl-11 pr-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white text-xs sm:text-sm font-bold text-slate-700 outline-none">
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">Coordenada norte/sur.</p>
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5">
                                Longitud GPS (Sede)
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-gray-400">explore</span>
                                <input type="number" 
                                       step="0.0000001" 
                                       name="longitud" 
                                       value="{{ old('longitud', $config->longitud ?? -70.4106000) }}" 
                                       placeholder="-70.4106000"
                                       class="w-full h-10.5 pl-11 pr-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white text-xs sm:text-sm font-bold text-slate-700 outline-none">
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">Coordenada este/oeste.</p>
                        </div>

                        <div class="md:col-span-4 flex flex-col justify-end">
                            <a href="{{ route('admin.zonas_delivery.index') }}" class="h-10.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200/80 border border-slate-200 text-slate-700 hover:text-slate-900 transition-all flex items-center justify-between text-xs font-bold shadow-2xs group">
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-rose-500">pin_drop</span>
                                    <span>Ver en Mapa</span>
                                </span>
                                <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                            </a>
                            <p class="text-[11px] text-gray-500 mt-1 ml-0.5">Ajusta el marcador de la tienda en el mapa interactivo.</p>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 4: REDES SOCIALES Y PRESENCIA DIGITAL (3 COLUMNAS SIMÉTRICAS) --}}
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 sm:p-7">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3.5 mb-4">
                        <h3 class="text-base font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-600 text-[22px]">public</span>
                            Redes Sociales y Presencia Digital
                        </h3>
                        <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                            Enlaces Oficiales
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 mb-4">
                        Configura los enlaces directos a las cuentas oficiales para los iconos de redes sociales en el pie de página.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
                        
                        {{-- Instagram --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5 flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-rose-500"></span>
                                Instagram URL
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 flex items-center justify-center text-rose-500">
                                    <svg class="size-4.5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </span>
                                <input type="text" 
                                       name="red_instagram" 
                                       value="{{ old('red_instagram', $config->red_instagram ?? 'https://instagram.com') }}" 
                                       placeholder="https://instagram.com/tu_cuenta"
                                       class="w-full h-10.5 pl-11 pr-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition-all font-medium text-slate-700 text-xs sm:text-sm outline-none">
                            </div>
                        </div>

                        {{-- Facebook --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5 flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-blue-600"></span>
                                Facebook URL
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 flex items-center justify-center text-blue-600">
                                    <svg class="size-4.5 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.657 5H18V0h-3.808C10.596 0 9 1.583 9 4.615V8z"/></svg>
                                </span>
                                <input type="text" 
                                       name="red_facebook" 
                                       value="{{ old('red_facebook', $config->red_facebook ?? 'https://facebook.com') }}" 
                                       placeholder="https://facebook.com/tu_pagina"
                                       class="w-full h-10.5 pl-11 pr-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-600/20 transition-all font-medium text-slate-700 text-xs sm:text-sm outline-none">
                            </div>
                        </div>

                        {{-- TikTok --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5 ml-0.5 flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-slate-900"></span>
                                TikTok URL (Opcional)
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[20px] text-gray-500">music_note</span>
                                <input type="text" 
                                       name="red_tiktok" 
                                       value="{{ old('red_tiktok', $config->red_tiktok) }}" 
                                       placeholder="https://tiktok.com/@tu_cuenta"
                                       class="w-full h-10.5 pl-11 pr-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-800/20 transition-all font-medium text-slate-700 text-xs sm:text-sm outline-none">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- SECCIÓN 5: MODALIDADES GLOBALES DE PRECIO Y RECARGOS (3 COLUMNAS SIMÉTRICAS) --}}
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 sm:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3.5 mb-5">
                        <div>
                            <h3 class="text-base font-black text-agro-dark flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-[22px]">price_change</span>
                                Modalidades de Precio y Recargos Globales
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Define el porcentaje adicional (%) para los 3 métodos de venta y su ámbito de aplicación.
                            </p>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 w-fit">
                            Ajuste Global
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
                        
                        {{-- 1. MODALIDAD DIVISAS --}}
                        <div class="bg-gradient-to-b from-emerald-50/50 to-white rounded-2xl border border-emerald-200/80 p-4.5 flex flex-col justify-between shadow-2xs">
                            <div>
                                <div class="flex items-center gap-2.5 mb-3.5">
                                    <div class="size-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <span class="material-symbols-outlined text-[18px]">attach_money</span>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-black text-slate-800 leading-tight">Divisas (USD / USDT)</h5>
                                        <span class="text-[10px] text-emerald-700 font-bold">Zelle, Binance, Efectivo $</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">
                                            Recargo % sobre PVP
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2.5 text-xs font-black text-emerald-700">%</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   name="recargo_divisas_pct" 
                                                   id="recargo_divisas_pct" 
                                                   value="{{ old('recargo_divisas_pct', $config->recargo_divisas_pct ?? 0.00) }}" 
                                                   oninput="actualizarSimuladorPrecios()"
                                                   required 
                                                   class="limit-decimals w-full h-10 pl-7 pr-3 rounded-xl bg-white border border-emerald-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 font-black text-emerald-800 text-sm outline-none transition-all">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">
                                            Ámbito de Aplicación
                                        </label>
                                        <select name="recargo_divisas_aplica" class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none cursor-pointer focus:border-emerald-500">
                                            <option value="ambas" {{ ($config->recargo_divisas_aplica ?? 'ambas') === 'ambas' ? 'selected' : '' }}>Tienda Web y POS (Ambas)</option>
                                            <option value="web" {{ ($config->recargo_divisas_aplica ?? '') === 'web' ? 'selected' : '' }}>Solo Tienda Digital (Web)</option>
                                            <option value="pos" {{ ($config->recargo_divisas_aplica ?? '') === 'pos' ? 'selected' : '' }}>Solo Tienda Física (POS)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3.5 pt-2.5 border-t border-emerald-100 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 font-medium">Ejemplo $10.00:</span>
                                <strong class="text-emerald-800 font-black" id="simulador_divisas_badge">$10.00 USD</strong>
                            </div>
                        </div>

                        {{-- 2. MODALIDAD BOLÍVARES --}}
                        <div class="bg-gradient-to-b from-blue-50/50 to-white rounded-2xl border border-blue-200/80 p-4.5 flex flex-col justify-between shadow-2xs">
                            <div>
                                <div class="flex items-center gap-2.5 mb-3.5">
                                    <div class="size-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-black text-slate-800 leading-tight">Bolívares (VES)</h5>
                                        <span class="text-[10px] text-blue-700 font-bold">Pago Móvil, Punto, Transferencias</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">
                                            Recargo % sobre PVP
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2.5 text-xs font-black text-blue-700">%</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   name="recargo_bolivares_pct" 
                                                   id="recargo_bolivares_pct" 
                                                   value="{{ old('recargo_bolivares_pct', $config->recargo_bolivares_pct ?? 0.00) }}" 
                                                   oninput="actualizarSimuladorPrecios()"
                                                   required 
                                                   class="limit-decimals w-full h-10 pl-7 pr-3 rounded-xl bg-white border border-blue-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 font-black text-blue-800 text-sm outline-none transition-all">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">
                                            Ámbito de Aplicación
                                        </label>
                                        <select name="recargo_bolivares_aplica" class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none cursor-pointer focus:border-blue-500">
                                            <option value="ambas" {{ ($config->recargo_bolivares_aplica ?? 'ambas') === 'ambas' ? 'selected' : '' }}>Tienda Web y POS (Ambas)</option>
                                            <option value="web" {{ ($config->recargo_bolivares_aplica ?? '') === 'web' ? 'selected' : '' }}>Solo Tienda Digital (Web)</option>
                                            <option value="pos" {{ ($config->recargo_bolivares_aplica ?? '') === 'pos' ? 'selected' : '' }}>Solo Tienda Física (POS)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3.5 pt-2.5 border-t border-blue-100 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 font-medium">Ejemplo $10.00:</span>
                                <strong class="text-blue-800 font-black" id="simulador_bolivares_badge">$12.00 ref.</strong>
                            </div>
                        </div>

                        {{-- 3. MODALIDAD CASHEA --}}
                        <div class="bg-gradient-to-b from-purple-50/50 to-white rounded-2xl border border-purple-200/80 p-4.5 flex flex-col justify-between shadow-2xs">
                            <div>
                                <div class="flex items-center gap-2.5 mb-3.5">
                                    <div class="size-8 rounded-lg bg-[#5800FD] text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <span class="material-symbols-outlined text-[18px]">credit_score</span>
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-black text-slate-800 leading-tight">Cashea (Cuotas)</h5>
                                        <span class="text-[10px] text-purple-700 font-bold">Financiamiento y Cuotas</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">
                                            Recargo % sobre PVP
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2.5 text-xs font-black text-purple-700">%</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   name="recargo_cashea_pct" 
                                                   id="recargo_cashea_pct" 
                                                   value="{{ old('recargo_cashea_pct', $config->recargo_cashea_pct ?? 0.00) }}" 
                                                   oninput="actualizarSimuladorPrecios()"
                                                   required 
                                                   class="limit-decimals w-full h-10 pl-7 pr-3 rounded-xl bg-white border border-purple-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 font-black text-purple-900 text-sm outline-none transition-all">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">
                                            Ámbito de Aplicación
                                        </label>
                                        <select name="recargo_cashea_aplica" class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 outline-none cursor-pointer focus:border-purple-500">
                                            <option value="ambas" {{ ($config->recargo_cashea_aplica ?? 'ambas') === 'ambas' ? 'selected' : '' }}>Tienda Web y POS (Ambas)</option>
                                            <option value="web" {{ ($config->recargo_cashea_aplica ?? '') === 'web' ? 'selected' : '' }}>Solo Tienda Digital (Web)</option>
                                            <option value="pos" {{ ($config->recargo_cashea_aplica ?? '') === 'pos' ? 'selected' : '' }}>Solo Tienda Física (POS)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3.5 pt-2.5 border-t border-purple-100 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 font-medium">Ejemplo $10.00:</span>
                                <strong class="text-purple-900 font-black" id="simulador_cashea_badge">$11.50 USD</strong>
                            </div>
                        </div>

                    </div>

                    {{-- Barra resumen del simulador --}}
                    <div class="mt-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[19px] text-slate-500">calculate</span>
                            <span class="text-slate-600 font-medium text-[11px]">
                                Ejemplo en base a un artículo con PVP de <strong>$10.00</strong>:
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] font-bold flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300" id="badge_resumen_divisas">Divisas: $10.00 (+0%)</span>
                            <span class="px-2.5 py-0.5 rounded-lg bg-blue-100 text-blue-800 border border-blue-300" id="badge_resumen_bs">Bolívares: $12.00 (+20%)</span>
                            <span class="px-2.5 py-0.5 rounded-lg bg-purple-100 text-purple-800 border border-purple-300" id="badge_resumen_cashea">Cashea: $11.50 (+15%)</span>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 6: OPERATIVIDAD Y DISPONIBILIDAD DE SERVICIOS (2 COLUMNAS SIMÉTRICAS) --}}
                <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 sm:p-7">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3.5 mb-5">
                        <h3 class="text-base font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500 text-[22px]">tune</span>
                            Operatividad y Disponibilidad de Servicios
                        </h3>
                        <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-md bg-orange-50 text-orange-700 border border-orange-200">
                            Disponibilidad
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                        
                        {{-- Columna 1: Operatividad Web --}}
                        <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-4.5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <label class="text-[11px] font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px] text-orange-500">power_settings_new</span>
                                        Compras en la Tienda Web
                                    </label>
                                    <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                        Modo Operativo
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <select name="modo_operativo" id="modo_operativo" onchange="verificarModo()" class="w-full h-10.5 px-3 rounded-xl bg-white border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all font-bold text-agro-dark text-xs sm:text-sm outline-none cursor-pointer">
                                        <option value="automatico" {{ $config->modo_operativo == 'automatico' ? 'selected' : '' }}>Automático (Abre y cierra según horario)</option>
                                        <option value="manual_abierto" {{ $config->modo_operativo == 'manual_abierto' ? 'selected' : '' }}>Forzar Abierto 24/7 (Permite compras siempre)</option>
                                        <option value="manual_cerrado" {{ $config->modo_operativo == 'manual_cerrado' ? 'selected' : '' }}>Cierre de Emergencia (Pausa temporal de compras)</option>
                                    </select>
                                </div>

                                <div id="caja_mensaje_cierre" class="transition-all duration-300 {{ $config->modo_operativo == 'manual_cerrado' ? 'bg-red-50 border border-red-200 p-3 rounded-xl mt-2' : '' }}">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider mb-1 {{ $config->modo_operativo == 'manual_cerrado' ? 'text-red-700' : 'text-slate-600' }}">Mensaje visible si la tienda está cerrada</label>
                                    <textarea name="mensaje_cierre_emergencia" rows="2" class="w-full p-2.5 rounded-xl bg-white border border-gray-200 focus:border-orange-500 outline-none text-xs text-gray-700 font-medium">{{ old('mensaje_cierre_emergencia', $config->mensaje_cierre_emergencia) }}</textarea>
                                    <p class="text-[10px] text-gray-500 mt-1">Sustituye el botón "Añadir al Carrito" en caso de cierre.</p>
                                </div>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 text-[11px] text-slate-500">
                                <span>Controla la capacidad de compra de los usuarios en línea.</span>
                            </div>
                        </div>

                        {{-- Columna 2: Servicio de Delivery / Despacho --}}
                        <div class="rounded-2xl border transition-all p-4.5 flex flex-col justify-between {{ $config->delivery_activo ? 'bg-emerald-50/50 border-emerald-200' : 'bg-amber-50/50 border-amber-200' }}" id="contenedor_delivery_toggle">
                            <div>
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="size-10 rounded-xl {{ $config->delivery_activo ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center shrink-0 shadow-2xs" id="icono_delivery_toggle">
                                            <span class="material-symbols-outlined text-[22px]">{{ $config->delivery_activo ? 'local_shipping' : 'pause_circle' }}</span>
                                        </div>
                                        <div>
                                            <h5 class="text-xs sm:text-sm font-black text-slate-800 leading-tight">Servicio de Delivery</h5>
                                            <span id="badge_delivery_toggle" class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider {{ $config->delivery_activo ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                                {{ $config->delivery_activo ? 'Despacho Disponible' : 'Pausado / No Disponible' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="hidden" name="delivery_activo_presente" value="1">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="delivery_activo" id="delivery_activo" value="1" {{ $config->delivery_activo ? 'checked' : '' }} onchange="actualizarEstadoDelivery(this)" class="sr-only peer">
                                            <div class="w-13 h-7 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600"></div>
                                        </label>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    Determina si los clientes pueden seleccionar entregas a domicilio en el Checkout y actualiza la insignia en el Top-Bar público.
                                </p>
                            </div>

                            <div class="mt-3.5 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 font-medium">Zonas y coberturas:</span>
                                <a href="{{ route('admin.zonas_delivery.index') }}" class="font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 hover:underline">
                                    <span>Gestionar Tarifas</span>
                                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Auditoría Footer (Separada con amplio margen para no ser tapada) --}}
                <div class="mt-2 mb-8 px-4 py-3 rounded-2xl bg-white border border-gray-200/80 shadow-2xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs font-semibold text-gray-500">
                    <p class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-gray-400">history</span>
                        <span>Última actualización:</span>
                        <strong class="text-slate-700">{{ \Carbon\Carbon::parse($config->actualizado_at)->format('d/m/Y h:i A') }}</strong>
                    </p>
                    <p class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-gray-400">person</span>
                        <span>Editado por:</span>
                        <strong class="text-slate-700">{{ $config->ultimoEditor ? $config->ultimoEditor->nombre : 'Sistema' }}</strong>
                    </p>
                </div>

                {{-- Botón Guardar Flotante Sticky Responsive --}}
                <div class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white/95 backdrop-blur-md border-t border-gray-200 p-3 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 z-40 shadow-[0_-10px_30px_rgba(0,0,0,0.06)]">
                    <div class="hidden md:flex items-center gap-3 text-xs font-medium text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">verified_user</span>
                            <span>Los cambios se aplican de forma inmediata en la Tienda Web y en el Punto de Venta (POS).</span>
                        </span>
                    </div>

                    <div class="w-full sm:w-auto flex items-center justify-end">
                        @if(Auth::user()->tienePermiso('configurar_tienda'))
                        <button type="submit" class="w-full sm:w-auto px-7 py-2.5 sm:py-3 rounded-xl font-black text-white bg-gray-900 hover:bg-black shadow-lg shadow-gray-900/25 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-[20px]">save</span> 
                            <span>Guardar Configuración</span>
                        </button>
                        @else
                        <button type="button" disabled class="w-full sm:w-auto px-7 py-2.5 sm:py-3 rounded-xl font-bold text-gray-400 bg-gray-200 cursor-not-allowed flex items-center justify-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-[20px]">lock</span> 
                            <span>Solo Lectura</span>
                        </button>
                        @endif
                    </div>
                </div>

            </form>

        </div>
    </main>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    // 1. Mostrar Notificaciones de Sesión
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('delivery_cambiado'))
        setTimeout(() => {
            if (typeof window.enviarNotificacionNavegadorDelivery === 'function') {
                window.enviarNotificacionNavegadorDelivery({{ session('delivery_cambiado')['activo'] ? 'true' : 'false' }});
            }
        }, 300);
    @endif
    
    @if(isset($errors) && $errors->any())
        showToast("Revisa los errores en el formulario.", 'error');
    @endif

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if(!container) return;
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? 'check_circle' : 'error';

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `<span class="material-symbols-outlined">${icon}</span><p class="font-bold text-sm">${message}</p>`;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // 2. Control Visual del Modo Operativo
    function verificarModo() {
        const select = document.getElementById('modo_operativo');
        const cajaMensaje = document.getElementById('caja_mensaje_cierre');
        if (!select || !cajaMensaje) return;
        const labelMensaje = cajaMensaje.querySelector('label');

        if (select.value === 'manual_cerrado') {
            cajaMensaje.classList.add('bg-red-50', 'border', 'border-red-200', 'p-3', 'rounded-xl', 'mt-2');
            if(labelMensaje) {
                labelMensaje.classList.remove('text-slate-600');
                labelMensaje.classList.add('text-red-700');
            }
        } else {
            cajaMensaje.classList.remove('bg-red-50', 'border', 'border-red-200', 'p-3', 'rounded-xl', 'mt-2');
            if(labelMensaje) {
                labelMensaje.classList.remove('text-red-700');
                labelMensaje.classList.add('text-slate-600');
            }
        }
    }

    // 3. Control reactivo del Switch de Delivery / Despacho
    function actualizarEstadoDelivery(checkbox) {
        const contenedor = document.getElementById('contenedor_delivery_toggle');
        const icono = document.getElementById('icono_delivery_toggle');
        const badge = document.getElementById('badge_delivery_toggle');
        const activo = checkbox.checked;

        if (contenedor && icono && badge) {
            if (activo) {
                contenedor.className = "rounded-2xl border transition-all p-4.5 flex flex-col justify-between bg-emerald-50/50 border-emerald-200";
                icono.className = "size-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-2xs";
                icono.innerHTML = '<span class="material-symbols-outlined text-[22px]">local_shipping</span>';
                badge.className = "text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300";
                badge.textContent = "Despacho Disponible";
            } else {
                contenedor.className = "rounded-2xl border transition-all p-4.5 flex flex-col justify-between bg-amber-50/50 border-amber-200";
                icono.className = "size-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-2xs";
                icono.innerHTML = '<span class="material-symbols-outlined text-[22px]">pause_circle</span>';
                badge.className = "text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300";
                badge.textContent = "Pausado / No Disponible";
            }
        }

        if (typeof window.enviarNotificacionNavegadorDelivery === 'function') {
            window.enviarNotificacionNavegadorDelivery(activo);
        }

        fetch('{{ route("admin.zonas_delivery.toggle_master") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ estado: activo ? 1 : 0 })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showToast(data.message || 'Error al actualizar el estado de delivery', 'error');
            }
        })
        .catch(err => {
            console.error('Error al sincronizar delivery:', err);
        });
    }

    // 4. Preview dinámico de número de WhatsApp
    function actualizarPreviewWhatsApp(val) {
        let soloDigitos = (val || '').replace(/[^0-9]/g, '');
        if (soloDigitos.startsWith('0')) {
            soloDigitos = '58' + soloDigitos.substring(1);
        }
        if (!soloDigitos) soloDigitos = '584129614615';
        
        const txt = document.getElementById('txt_preview_wa');
        const link = document.getElementById('link_preview_wa');
        if (txt) txt.textContent = soloDigitos;
        if (link) link.href = 'https://wa.me/' + soloDigitos;
    }

    // 5. Simulador interactivo de modalidades de precio
    function actualizarSimuladorPrecios() {
        const base = 10.00;
        const pctDiv = parseFloat(document.getElementById('recargo_divisas_pct')?.value || 0) || 0;
        const pctBs = parseFloat(document.getElementById('recargo_bolivares_pct')?.value || 0) || 0;
        const pctCash = parseFloat(document.getElementById('recargo_cashea_pct')?.value || 0) || 0;

        const valDiv = base * (1 + (pctDiv / 100));
        const valBs = base * (1 + (pctBs / 100));
        const valCash = base * (1 + (pctCash / 100));

        const badgeDiv = document.getElementById('simulador_divisas_badge');
        const badgeBs = document.getElementById('simulador_bolivares_badge');
        const badgeCash = document.getElementById('simulador_cashea_badge');

        const resDiv = document.getElementById('badge_resumen_divisas');
        const resBs = document.getElementById('badge_resumen_bs');
        const resCash = document.getElementById('badge_resumen_cashea');

        if (badgeDiv) badgeDiv.textContent = '$' + valDiv.toFixed(2) + ' USD';
        if (badgeBs) badgeBs.textContent = '$' + valBs.toFixed(2) + ' ref.';
        if (badgeCash) badgeCash.textContent = '$' + valCash.toFixed(2) + ' USD';

        if (resDiv) resDiv.textContent = `Divisas: $${valDiv.toFixed(2)} (${pctDiv >= 0 ? '+' : ''}${pctDiv.toFixed(1)}%)`;
        if (resBs) resBs.textContent = `Bolívares: $${valBs.toFixed(2)} (${pctBs >= 0 ? '+' : ''}${pctBs.toFixed(1)}%)`;
        if (resCash) resCash.textContent = `Cashea: $${valCash.toFixed(2)} (${pctCash >= 0 ? '+' : ''}${pctCash.toFixed(1)}%)`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        actualizarSimuladorPrecios();
        const inputWa = document.getElementById('input_whatsapp');
        if (inputWa) actualizarPreviewWhatsApp(inputWa.value);
    });
</script>
@endpush
@endsection