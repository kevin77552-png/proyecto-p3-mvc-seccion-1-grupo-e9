<div class="p-6">
    @if($module === 'admin.users')
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
            <livewire:admin.users />
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'admin.users.register')
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
            <div class="bg-white p-4 rounded shadow max-w-2xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-blue-800 mb-4 text-center">Registrar nuevo usuario</h2>
                @livewire('pages.auth.register')
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'admin.users.list')
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
            <div class="bg-white p-4 rounded shadow max-w-6xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-blue-800 mb-4 text-center">Consultar Usuarios</h2>
                @livewire('admin.user-list')
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'admin.inventory.ingreso')
        <livewire:admin.inventory-ingreso />
    @elseif($module === 'admin.inventory.egreso')
        <livewire:admin.inventory-egreso />
    @elseif($module === 'admin.inventory.consulta')
        <livewire:admin.inventory-consulta />
    @elseif($module === 'admin.profile')
        <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
            <h2 class="text-2xl font-bold text-blue-800 mb-8 text-center">Perfil de usuario</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="border border-gray-300 rounded-lg p-6 bg-gray-50 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center flex items-center justify-center gap-2">
                        <span class="material-icons text-blue-500">person</span>
                        Actualizar información
                    </h3>
                    <livewire:profile.update-profile-information-form />
                </div>
                <div class="border border-gray-300 rounded-lg p-6 bg-gray-50 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center flex items-center justify-center gap-2">
                        <span class="material-icons text-green-500">lock</span>
                        Actualizar contraseña
                    </h3>
                    <livewire:profile.update-password-form />
                </div>
            </div>
            {{-- <livewire:profile.delete-user-form /> --}}
        </div>
    @elseif($module === 'admin.history')
        <livewire:admin.history />
    @elseif($module === 'admin.reports')
        <livewire:admin.reports />
    @elseif($module === 'admin.migration')
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
            <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Migración de datos</h2>
                <livewire:admin.migration />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'provider.purchase_orders')
        @if(auth()->user() && auth()->user()->role === 'proveedor')
            <div class="bg-white p-4 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-4 text-center">Órdenes de compra</h2>
                <livewire:provider.purchase-orders />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'admin.purchase_orders')
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador', 'gerente']))
            <div class="bg-white p-4 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-4 text-center">Órdenes de compra</h2>
                <livewire:admin.purchase-orders />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'admin.purchase_orders.consultar')
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
            <div class="bg-white p-4 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-4 text-center">Consultar Órdenes de compra</h2>
                <livewire:admin.purchase-orders-consulta />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    <!-- 'Resumen' removed for gerente; only 'consultar' remains -->
    @elseif($module === 'gerente.purchase_orders.consultar')
        @if(auth()->user() && auth()->user()->role === 'gerente')
            <div class="bg-white p-4 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-4 text-center">Consultar Órdenes de compra</h2>
                <livewire:gerente.purchase-orders-consulta />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'admin.settings')
        <livewire:admin.settings />
    @elseif($module === 'admin.settings.registro')
        <livewire:admin.settings-registro />
    @elseif($module === 'gerente.solicitudes')
        @if(auth()->user() && auth()->user()->role === 'gerente')
            <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-4 text-center">Solicitudes</h2>
                <livewire:gerente.solicitudes />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'gerente.eliminaciones')
        @if(auth()->user() && auth()->user()->role === 'gerente')
            <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-4 text-center">Eliminaciones</h2>
                <livewire:gerente.eliminaciones />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'almacenista.solicitar_repuestos')
        @if(auth()->user() && auth()->user()->role === 'almacenista')
            <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-green-800 mb-4 text-center">Solicitar Repuestos</h2>
                <livewire:almacenista.solicitar-repuestos />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'almacenista.solicitudes')
        @if(auth()->user() && auth()->user()->role === 'almacenista')
            <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-green-800 mb-4 text-center">Mis solicitudes</h2>
                <livewire:almacenista.solicitudes />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">No tienes permiso para acceder a este módulo.</div>
        @endif
    @elseif($module === 'wiki.ayuda')
        @if(auth()->user())
            <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
                <h2 class="text-2xl font-bold text-teal-800 mb-4 text-center">Wiki Ayuda</h2>
                <livewire:wiki.ayuda />
            </div>
        @else
            <div class="bg-red-100 text-red-800 p-4 rounded">Inicie sesión para ver la Wiki.</div>
        @endif
    @else
        <div class="text-gray-500">Selecciona una opción del menú lateral.</div>
    @endif
</div>
