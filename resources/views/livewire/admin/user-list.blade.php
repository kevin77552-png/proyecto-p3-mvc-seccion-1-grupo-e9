<div>
<div class="bg-white p-4 rounded shadow">
    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 mb-4 gap-2">
        <form wire:submit.prevent="applyFilters" class="flex flex-col md:flex-row md:items-center md:space-x-4 gap-2 w-full">
            <div>
                <input type="text" wire:model.defer="filterName" placeholder="Buscar por nombre..." class="border rounded px-2 py-1 w-full md:w-48" />
            </div>
            <div>
                    <select wire:model.defer="filterRole" class="border rounded px-2 py-1 w-full md:w-40">
                    <option value="">Todos los roles</option>
                    @if(!(auth()->user() && auth()->user()->role === 'administrador'))
                        <option value="administrador">Administrador</option>
                        <option value="superadministrador">Superadministrador</option>
                    @endif
                     	<option value="gerente">Gerente</option>
                    <option value="proveedor">Proveedor</option>
                    <option value="almacenista">Almacenista</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">Buscar</button>
            </div>
        </form>
    </div>
    <table class="min-w-full divide-y divide-gray-200 mb-4">
        <thead>
            <tr>
                <th class="px-4 py-2">Nombre</th>
                <th class="px-4 py-2">Correo</th>
                <th class="px-4 py-2">Rol</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @php $meRole = auth()->user()->role ?? null; @endphp
            @foreach($users as $user)
                <tr class="text-center {{ $loop->odd ? 'bg-white' : 'bg-blue-50' }} {{ !$user->activo ? 'bg-gray-200 text-gray-500' : '' }}">
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">
                        @php
                            $masked = $user->email;
                            if (auth()->user() && auth()->user()->role === 'administrador' && $user->role === 'superadministrador') {
                                $email = $user->email ?? '';
                                if (strpos($email, '@') !== false) {
                                    [$local, $domain] = explode('@', $email, 2);
                                    $localLen = strlen($local);
                                    $prefix = $localLen > 0 ? substr($local, 0, 1) : '';
                                    $starsCount = max(3, max(0, $localLen - 1));
                                    $stars = str_repeat('*', $starsCount);
                                    $masked = $prefix . $stars . '@' . $domain;
                                } else {
                                    $masked = 'Oculto';
                                }
                            }
                        @endphp
                        {{ $masked }}
                    </td>
                    <td class="px-4 py-2 capitalize">{{ $user->role }}</td>
                    <td class="px-4 py-2 space-x-2">
                        @if(
                            ($meRole === 'superadministrador' && $user->role !== 'superadministrador') ||
                            ($meRole === 'administrador' && !in_array($user->role, ['administrador','superadministrador']))
                        )
                            <button wire:click="editUser({{ $user->id }})" class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded">Editar</button>
                            @if($user->activo)
                                <button 
                                    x-data="{}"
                                    @click.prevent="Swal.fire({
                                        title: '¿Estás seguro?',
                                        text: 'Esta acción deshabilitará al usuario.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Sí, deshabilitar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.disableUser({{ $user->id }})
                                        }
                                    })"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                    Deshabilitar
                                </button>
                            @else
                                <button wire:click="enableUser({{ $user->id }})" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">Habilitar</button>
                            @endif
                        @else
                            {{-- Mostrar botones deshabilitados con tooltip explicativo --}}
                            <button class="bg-gray-200 text-gray-600 px-2 py-1 rounded cursor-not-allowed" disabled title="Acción no permitida para tu rol: no puedes editar o deshabilitar este usuario">Editar</button>
                            @if($user->activo)
                                <button class="bg-gray-200 text-gray-600 px-2 py-1 rounded cursor-not-allowed" disabled title="Acción no permitida para tu rol: no puedes editar o deshabilitar este usuario">Deshabilitar</button>
                            @else
                                <button class="bg-gray-200 text-gray-600 px-2 py-1 rounded cursor-not-allowed" disabled title="Acción no permitida para tu rol: no puedes editar o habilitar este usuario">Habilitar</button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}

    @if($editId)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h4 class="text-lg font-bold mb-4">Editar usuario</h4>
                @if(session('error'))
                    <div class="mb-2 p-2 rounded bg-red-100 text-red-800 text-center font-semibold">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="mb-2">
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text" wire:model="editName" class="w-full border rounded px-2 py-1" />
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Correo</label>
                    <input type="email" wire:model="editEmail" class="w-full border rounded px-2 py-1" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Rol</label>
                    <select wire:model="editRole" class="w-full border rounded px-2 py-1">
                        @if(!(auth()->user() && auth()->user()->role === 'administrador'))
                            <option value="administrador">Administrador</option>
                            <option value="superadministrador">Superadministrador</option>
                        @endif
                        <option value="gerente">Gerente</option>
                        <option value="proveedor">Proveedor</option>
                        <option value="almacenista">Almacenista</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button wire:click="cancelEdit" class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400">Cancelar</button>
                    <button wire:click="updateUser" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
