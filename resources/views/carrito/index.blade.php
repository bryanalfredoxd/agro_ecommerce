@extends('layouts.app')

@section('title', 'Mi Carrito de Compras')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-black text-agro-dark mb-8 flex items-center gap-3">
            <span class="material-symbols-outlined text-4xl text-primary">shopping_cart</span>
            Tu Carrito
        </h1>

        @if($items->count() > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- Lista de Productos --}}
            <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    @foreach($items as $item)
                        <div class="flex flex-col sm:flex-row items-center gap-6 py-6 border-b border-gray-100 last:border-0" id="item-{{ $item->id }}">
                            <div class="w-24 h-24 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-200">
                                @if($item->producto->imagenes->first())
                                    <img src="{{ $item->producto->imagenes->first()->url_imagen }}" alt="{{ $item->producto->nombre }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <span class="material-symbols-outlined text-3xl">image</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="text-lg font-bold text-gray-800">{{ $item->producto->nombre }}</h3>
                                <p class="text-sm text-gray-500 mb-2">{{ $item->producto->sku }}</p>
                                <p class="text-primary font-bold text-xl">${{ number_format($item->producto->precio_venta_usd, 2) }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <button onclick="updateQty({{ $item->id }}, -1)" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center font-bold transition">-</button>
                                <input type="number" readonly value="{{ number_format($item->cantidad, 0) }}" class="w-12 text-center font-bold text-gray-800 border-none bg-transparent focus:ring-0">
                                <button onclick="updateQty({{ $item->id }}, 1)" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center font-bold transition">+</button>
                            </div>

                            <div class="w-24 text-right hidden sm:block">
                                <span class="block text-sm text-gray-400">Total</span>
                                <span class="font-bold text-gray-800">${{ number_format($item->producto->precio_venta_usd * $item->cantidad, 2) }}</span>
                            </div>

                            <button onclick="openDeleteModal({{ $item->id }})" class="text-red-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Resumen de Compra --}}
            <div class="w-full lg:w-96">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Resumen del Pedido</h2>
                    
                    <div class="space-y-4 mb-6 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>IVA ({{ $ivaPorcentaje }}%)</span>
                            <span>${{ number_format($montoIva, 2) }}</span>
                        </div>
                        <div class="h-px bg-gray-200 my-2"></div>
                        <div class="flex justify-between text-lg font-black text-agro-dark">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    {{-- CAMBIO PRINCIPAL: Enlace a la ruta checkout.index --}}
                    <a href="{{ route('checkout.index') }}" class="w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:bg-primary-dark hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                        Proceder al Pago
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    
                    <a href="{{ route('catalogo') }}" class="block text-center mt-4 text-sm text-gray-500 hover:text-primary font-medium">
                        Seguir comprando
                    </a>
                </div>
            </div>

        </div>
        @else
        {{-- Estado Vacío --}}
        <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-dashed border-gray-300">
            <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-primary/50">shopping_cart_off</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Tu carrito está vacío</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">Parece que aún no has agregado insumos. Explora nuestro catálogo para encontrar lo que necesitas.</p>
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-agro-dark text-white rounded-xl font-bold hover:bg-gray-800 transition">
                Ir al Catálogo
            </a>
        </div>
        @endif

    </div>
</div>

{{-- TOAST NOTIFICATION --}}
<div id="toast-notification" class="fixed bottom-6 right-6 z-[100] transform transition-all duration-500 translate-y-24 opacity-0 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 p-4 flex items-center gap-4 min-w-[320px] max-w-md">
        <div id="toast-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors">
            <span id="toast-icon" class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div class="flex-1">
            <h4 id="toast-title" class="font-bold text-gray-900 text-sm">Notificación</h4>
            <p id="toast-message" class="text-xs font-medium text-gray-500 mt-0.5">Mensaje...</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
</div>

{{-- MODAL DE ELIMINACIÓN --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="delete-modal-backdrop"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="delete-modal-panel">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">¿Eliminar producto?</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar este producto de tu carrito? Esta acción no se puede deshacer.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="confirmDelete()" class="inline-flex w-full justify-center rounded-xl bg-red-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                        Sí, eliminar
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2.5 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Variables para el modal de eliminación
    let itemToDeleteId = null;

    // ========================
    // 🔔 Lógica de Toasts
    // ========================
    let toastTimeout;
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-notification');
        const iconContainer = document.getElementById('toast-icon-container');
        const icon = document.getElementById('toast-icon');
        const title = document.getElementById('toast-title');
        const msg = document.getElementById('toast-message');

        iconContainer.className = 'w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors';
        
        if (type === 'success') {
            iconContainer.classList.add('bg-green-50', 'text-green-600');
            icon.innerText = 'check_circle';
            title.innerText = '¡Éxito!';
            title.className = 'font-bold text-green-700 text-sm';
        } else {
            iconContainer.classList.add('bg-red-50', 'text-red-500');
            icon.innerText = 'error';
            title.innerText = 'Error';
            title.className = 'font-bold text-red-700 text-sm';
        }

        msg.innerText = message;
        toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => hideToast(), 4000);
    }

    function hideToast() {
        document.getElementById('toast-notification').classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }

    // ========================
    // 🗑️ Lógica de Modal
    // ========================
    function openDeleteModal(itemId) {
        itemToDeleteId = itemId;
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-modal-backdrop');
        const panel = document.getElementById('delete-modal-panel');

        modal.classList.remove('hidden');
        // Pequeño delay para permitir que el navegador renderice antes de animar
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        }, 10);
    }

    function closeDeleteModal() {
        itemToDeleteId = null;
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-modal-backdrop');
        const panel = document.getElementById('delete-modal-panel');

        backdrop.classList.add('opacity-0');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300); // Esperar a que termine la transición
    }

    function confirmDelete() {
        if (!itemToDeleteId) return;

        fetch("{{ route('carrito.remove') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ id: itemToDeleteId })
        })
        .then(res => res.json())
        .then(data => {
            closeDeleteModal();
            if (data.status === 'success') {
                showToast('Producto eliminado correctamente', 'success');
                // Recargar página para actualizar totales
                setTimeout(() => location.reload(), 500);
            } else {
                showToast('Error al eliminar producto', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            closeDeleteModal();
            showToast('Error de conexión', 'error');
        });
    }

    // ========================
    // 🛒 Lógica de Actualización
    // ========================
    function updateQty(itemId, change) {
        const input = document.querySelector(`#item-${itemId} input`);
        let newQty = parseInt(input.value) + change;
        
        if (newQty < 1) return;

        fetch("{{ route('carrito.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ id: itemId, cantidad: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                showToast(data.message || 'No hay suficiente stock disponible', 'error');
            }
        })
        .catch(err => console.error(err));
    }

    // ========================
    // 📢 Inicialización (Mensajes de Sesión)
    // ========================
    document.addEventListener("DOMContentLoaded", () => {
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
    });
</script>
@endsection