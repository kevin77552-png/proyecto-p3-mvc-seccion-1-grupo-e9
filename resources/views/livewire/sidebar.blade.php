

<div
    x-data="{
        open: true,
        submenu: null
    }"
    :class="open ? 'w-64' : 'w-16'"
    class="min-h-screen bg-[#540201] text-white flex flex-col p-4 space-y-2 relative sticky top-0 z-40 transition-all duration-300 ease-in-out shadow-lg"
    style="will-change: width;"
>
    <!-- Botón de toggle -->
    <button
        @click="open = !open"
        class="absolute -right-4 top-4 z-10 bg-gray-200 text-gray-700 rounded-full p-1 shadow hover:bg-gray-300 focus:outline-none transition-transform duration-300"
        aria-label="Toggle sidebar"
    >
        <!-- Flecha a la izquierda cuando está abierto -->
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <!-- Flecha a la derecha cuando está cerrado -->
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    

        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
        <div>
            <button @click="submenu === 'users' ? submenu = null : submenu = 'users'"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full transition-colors duration-200 {{ ($selected && ($selected === 'admin.users.register' || $selected === 'admin.users.list')) ? 'bg-blue-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-blue-400" :class="!open ? 'text-lg' : ''">group</span>
                <span x-show="open">Usuarios</span>
                <svg :class="[submenu === 'users' ? 'rotate-90' : '', !open ? 'hidden' : 'ml-auto']" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
            <div x-show="submenu === 'users' && open" class="pl-8 flex flex-col space-y-1 transition-all duration-200">
                <button wire:click="select('admin.users.register')" class="text-left px-4 py-2 rounded hover:bg-blue-700 {{ $selected === 'admin.users.register' ? 'bg-blue-700' : '' }}">Registrar usuario</button>
                <button wire:click="select('admin.users.list')" class="text-left px-4 py-2 rounded hover:bg-blue-700 {{ $selected === 'admin.users.list' ? 'bg-blue-700' : '' }}">Consultar usuarios</button>
            </div>
        </div>
        @endif

  @if(auth()->user() && auth()->user()->role === 'almacenista')
        <div>
            <!-- Inventario primero -->
            <button @click="submenu === 'inv' ? submenu = null : submenu = 'inv'"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full transition-colors duration-200 {{ ($selected && \Illuminate\Support\Str::startsWith($selected, 'admin.inventory')) ? 'bg-green-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-green-400" :class="!open ? 'text-lg' : ''">inventory_2</span>
                <span x-show="open">Inventario</span>
                <svg :class="[submenu === 'inv' ? 'rotate-90' : '', !open ? 'hidden' : 'ml-auto']" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
            <div x-show="submenu === 'inv' && open" class="pl-8 flex flex-col space-y-1 transition-all duration-200">
                <button wire:click="select('admin.inventory.ingreso')" class="text-left px-4 py-2 rounded hover:bg-green-700 {{ $selected === 'admin.inventory.ingreso' ? 'bg-green-700' : '' }}">
                    <span class="inline-block w-4 text-gray-200">-</span>
                    Ingreso
                </button>
                    <button wire:click="select('admin.inventory.egreso')" class="text-left px-4 py-2 rounded hover:bg-green-700 {{ $selected === 'admin.inventory.egreso' ? 'bg-green-700' : '' }}">
                        <span class="inline-block w-4 text-gray-200">-</span>
                        Salida
                    </button>
                <button wire:click="select('admin.inventory.consulta')" class="text-left px-4 py-2 rounded hover:bg-green-700 {{ $selected === 'admin.inventory.consulta' ? 'bg-green-700' : '' }}">
                    <span class="inline-block w-4 text-gray-200">-</span>
                    Consultar
                </button>
            </div>

            <!-- Solicitar repuestos en 2ª posición dentro del bloque almacenista -->
            <button wire:click="select('almacenista.solicitar_repuestos')"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 transition-colors duration-200 {{ $selected === 'almacenista.solicitar_repuestos' ? 'bg-green-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-green-400" :class="!open ? 'text-lg' : ''">add_shopping_cart</span>
                <span x-show="open">Solicitar repuestos</span>
            </button>
            <!-- Consultar solicitudes (mostrar solicitudes propias del almacenista) -->
            <button wire:click="select('almacenista.solicitudes')"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 transition-colors duration-200 {{ $selected === 'almacenista.solicitudes' ? 'bg-green-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-green-400" :class="!open ? 'text-lg' : ''">list_alt</span>
                <span x-show="open">Consultar solicitudes</span>
            </button>
        </div>
        @endif

        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
        <button wire:click="select('admin.history')"
            class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 transition-colors duration-200 {{ $selected === 'admin.history' ? 'bg-yellow-500 text-gray-900' : '' }}"
            :class="!open ? 'justify-center px-2' : ''"
        >
            <span class="material-icons text-yellow-400" :class="!open ? 'text-lg' : ''">history</span>
            <span x-show="open">Histórico</span>
        </button>
        @endif
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
        <button wire:click="select('admin.migration')"
            class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 transition-colors duration-200 {{ $selected === 'admin.migration' ? 'bg-gray-800' : '' }}"
            :class="!open ? 'justify-center px-2' : ''"
        >
            <span class="material-icons text-white" :class="!open ? 'text-lg' : ''">swap_vert</span>
            <span x-show="open">Migración</span>
        </button>
        @endif
        <button wire:click="select('admin.reports')"
            class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 transition-colors duration-200 {{ $selected === 'admin.reports' ? 'bg-purple-600' : '' }}"
            :class="!open ? 'justify-center px-2' : ''"
        >
            <span class="material-icons text-purple-400" :class="!open ? 'text-lg' : ''">bar_chart</span>
            <span x-show="open">Reportes</span>
        </button>
        <!-- Opción global: Wiki Ayuda (visible para todos los roles autenticados) -->
        @if(auth()->user())
            <button wire:click="select('wiki.ayuda')"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 transition-colors duration-200 {{ $selected === 'wiki.ayuda' ? 'bg-teal-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-teal-400" :class="!open ? 'text-lg' : ''">menu_book</span>
                <span x-show="open">Wiki Ayuda</span>
            </button>
        @endif
        
        {{-- Opción para proveedores: visualizar pedidos realizados por el administrador --}}
        @if(auth()->user() && auth()->user()->role === 'proveedor')
            <button wire:click="select('provider.purchase_orders')"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 transition-colors duration-200 {{ $selected === 'provider.purchase_orders' ? 'bg-indigo-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-indigo-400" :class="!open ? 'text-lg' : ''">receipt_long</span>
                <span x-show="open">Orden de compra</span>
            </button>
        @endif

        {{-- Opción para gerentes: resumen y consulta de OC --}}
        @if(auth()->user() && auth()->user()->role === 'gerente')
            <div>
                <button @click="submenu === 'oc' ? submenu = null : submenu = 'oc'"
                    class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full transition-colors duration-200 {{ ($selected && 
                        ($selected === 'admin.purchase_orders' || $selected === 'gerente.purchase_orders.consultar')) ? 'bg-indigo-600' : '' }}"
                    :class="!open ? 'justify-center px-2' : ''"
                >
                    <span class="material-icons text-indigo-400" :class="!open ? 'text-lg' : ''">receipt_long</span>
                    <span x-show="open">Órdenes de compra (OC)</span>
                    <svg :class="[submenu === 'oc' ? 'rotate-90' : '', !open ? 'hidden' : 'ml-auto']" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="submenu === 'oc' && open" class="pl-8 flex flex-col space-y-1 transition-all duration-200">
                    <!-- Crear pedido: reutiliza la vista/admin de creación -->
                    <button wire:click="select('admin.purchase_orders')" class="text-left px-4 py-2 rounded hover:bg-indigo-700 {{ $selected === 'admin.purchase_orders' ? 'bg-indigo-700' : '' }}">Crear pedido</button>
                    <!-- Consultar: vista específica de gerente con paginación -->
                    <button wire:click="select('gerente.purchase_orders.consultar')" class="text-left px-4 py-2 rounded hover:bg-indigo-700 {{ $selected === 'gerente.purchase_orders.consultar' ? 'bg-indigo-700' : '' }}">Consultar</button>
                </div>
            </div>
            <!-- Nueva opción para Gerente: Solicitudes -->
            <button wire:click="select('gerente.solicitudes')"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full transition-colors duration-200 {{ $selected === 'gerente.solicitudes' ? 'bg-indigo-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-indigo-400" :class="!open ? 'text-lg' : ''">assignment</span>
                <span x-show="open">Solicitudes</span>
            </button>
            <!-- Nueva opción para Gerente: Eliminaciones -->
            <button wire:click="select('gerente.eliminaciones')"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full transition-colors duration-200 {{ $selected === 'gerente.eliminaciones' ? 'bg-indigo-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-indigo-400" :class="!open ? 'text-lg' : ''">delete_forever</span>
                <span x-show="open">Eliminaciones</span>
            </button>
        @endif

        {{-- Opción para administradores: crear/gestionar Órdenes de compra (OC) --}}
        @if(auth()->user() && in_array(auth()->user()->role, ['administrador','superadministrador']))
            <div>
                <button @click="submenu === 'admin_oc' ? submenu = null : submenu = 'admin_oc'"
                    class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full transition-colors duration-200 {{ ($selected && ($selected === 'admin.purchase_orders' || $selected === 'admin.purchase_orders.consultar')) ? 'bg-indigo-600' : '' }}"
                    :class="!open ? 'justify-center px-2' : ''"
                >
                    <span class="material-icons text-indigo-400" :class="!open ? 'text-lg' : ''">receipt_long</span>
                    <span x-show="open">Órdenes de compra (OC)</span>
                    <svg :class="[submenu === 'admin_oc' ? 'rotate-90' : '', !open ? 'hidden' : 'ml-auto']" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <div x-show="submenu === 'admin_oc' && open" class="pl-8 flex flex-col space-y-1 transition-all duration-200">
                    <button wire:click="select('admin.purchase_orders')" class="text-left px-4 py-2 rounded hover:bg-indigo-700 {{ $selected === 'admin.purchase_orders' ? 'bg-indigo-700' : '' }}">Crear pedido</button>
                    <button wire:click="select('admin.purchase_orders.consultar')" class="text-left px-4 py-2 rounded hover:bg-indigo-700 {{ $selected === 'admin.purchase_orders.consultar' ? 'bg-indigo-700' : '' }}">Consultar</button>
                </div>
            </div>
        @endif
        
       <div class="flex flex-col space-y-2 mt-10">
        <div>
            <button @click="submenu === 'settings' ? submenu = null : submenu = 'settings'"
                class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full transition-colors duration-200 {{ $selected === 'admin.settings' ? 'bg-red-600' : '' }}"
                :class="!open ? 'justify-center px-2' : ''"
            >
                <span class="material-icons text-red-400" :class="!open ? 'text-lg' : ''">settings</span>
                <span x-show="open">Configuración</span>
                <svg :class="[submenu === 'settings' ? 'rotate-90' : '', !open ? 'hidden' : 'ml-auto']" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
            <div x-show="submenu === 'settings' && open" class="pl-8 flex flex-col space-y-1 transition-all duration-200">
                <button wire:click="select('admin.profile')" class="text-left px-4 py-2 rounded hover:bg-purple-700 {{ $selected === 'admin.profile' ? 'bg-purple-600' : '' }}">
                    <span class="material-icons text-purple-400 mr-2">person</span> Perfil
                </button>
                @if(auth()->user() && auth()->user()->role === 'superadministrador')
                    <button wire:click="select('admin.settings.registro')" class="text-left px-4 py-2 rounded hover:bg-red-700 {{ $selected === 'admin.settings.registro' ? 'bg-red-700' : '' }}">
                        <span class="material-icons text-red-400 mr-2">how_to_reg</span>
                        Registro
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
