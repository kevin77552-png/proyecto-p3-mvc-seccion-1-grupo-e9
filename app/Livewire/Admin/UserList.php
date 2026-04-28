<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class UserList extends Component
{
    public $filterName = '';
    public $filterRole = '';

    public function applyFilters()
    {
        $this->searchName = $this->filterName;
        $this->searchRole = $this->filterRole;
        $this->resetPage();
    }
    use WithPagination;

    public $editId = null;
    public $editName = '';
    public $editEmail = '';
    public $editRole = '';
    public $searchName = '';
    public $searchRole = '';

    protected $paginationTheme = 'tailwind';

    public function editUser($id)
    {
        $user = User::find($id);
        $current = Auth::user();
        // Reglas de edición:
        // - superadministrador puede editar cualquier usuario que NO sea superadministrador
        // - administrador puede editar usuarios que NO sean administrador ni superadministrador
        if ($user && $current) {
            if ($current->role === 'superadministrador' && $user->role !== 'superadministrador') {
                $this->editId = $user->id;
                $this->editName = $user->name;
                $this->editEmail = $user->email;
                $this->editRole = $user->role;
            } elseif ($current->role === 'administrador' && !in_array($user->role, ['administrador','superadministrador'])) {
                $this->editId = $user->id;
                $this->editName = $user->name;
                $this->editEmail = $user->email;
                $this->editRole = $user->role;
            }
        }
    }

    public function updateUser()
    {
        $user = User::find($this->editId);
        $current = Auth::user();
        // Reglas de actualización (mismas que edición):
        if ($user && $current) {
            $allowed = false;
            if ($current->role === 'superadministrador' && $user->role !== 'superadministrador') {
                $allowed = true;
            } elseif ($current->role === 'administrador' && !in_array($user->role, ['administrador','superadministrador'])) {
                $allowed = true;
            }
            if ($allowed) {
            // Si se intenta asignar el rol de administrador, validar el límite
                if ($this->editRole === 'administrador') {
                    $limit = (int) \App\Models\Setting::getValue('max_administradores', 2);
                    $adminCount = User::where('role', 'administrador')->where('activo', true)->count();
                    if ($adminCount >= $limit) {
                        session()->flash('error', "No se pueden tener más de {$limit} usuarios con rol administrador activos.");
                        return;
                    }
                }
            // Capturar valores originales para comparar
            $original = $user->only(['name', 'email', 'role', 'activo']);

            // Aplicar cambios
                $user->name = $this->editName;
                $user->email = $this->editEmail;
                $user->role = $this->editRole;
                $user->save();

            // Construir descripción detallada de cambios
            $changes = [];
            $fields = ['name', 'email', 'role', 'activo'];
            foreach ($fields as $field) {
                $old = isset($original[$field]) ? (string)$original[$field] : '';
                $new = (string)$user->{$field};
                if ($old !== $new) {
                    $changes[] = "$field: '$old' => '$new'";
                }
            }

            $desc = 'Edición de usuario ID ' . $user->id;
            if (count($changes) > 0) {
                $desc .= ' — Cambios: ' . implode('; ', $changes);
            } else {
                $desc .= ' — Sin cambios detectados.';
            }

            Log::create([
                'accion' => 'editar',
                'entidad' => 'user',
                'entidad_id' => $user->id,
                'descripcion' => $desc,
                'user_id' => Auth::id() ?? 0,
            ]);
                $this->editId = null;
            }
        }
    }

    public function cancelEdit()
    {
        $this->editId = null;
    }

    public function disableUser($id)
    {
        $user = User::find($id);
        $current = Auth::user();
        // Reglas de deshabilitado: similar a edición
        if ($user && $current && $user->id !== $current->id) {
            if ($current->role === 'superadministrador' && $user->role !== 'superadministrador') {
                $user->activo = false;
                $user->save();
                Log::create([
                    'accion' => 'deshabilitar',
                    'entidad' => 'user',
                    'entidad_id' => $user->id,
                    'descripcion' => 'Usuario deshabilitado: ' . $user->name,
                    'user_id' => Auth::id() ?? 0,
                ]);
            } elseif ($current->role === 'administrador' && !in_array($user->role, ['administrador','superadministrador'])) {
                $user->activo = false;
                $user->save();
                Log::create([
                    'accion' => 'deshabilitar',
                    'entidad' => 'user',
                    'entidad_id' => $user->id,
                    'descripcion' => 'Usuario deshabilitado: ' . $user->name,
                    'user_id' => Auth::id() ?? 0,
                ]);
            }
        }
    }

    public function enableUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->activo = true;
            $user->save();
            Log::create([
                'accion' => 'habilitar',
                'entidad' => 'user',
                'entidad_id' => $user->id,
                'descripcion' => 'Usuario habilitado: ' . $user->name,
                'user_id' => Auth::id() ?? 0,
            ]);
        }
    }
    public function render()
    {
        $query = \App\Models\User::query();
        if ($this->searchName) {
            $query->where('name', 'like', '%' . $this->searchName . '%');
        }
        if ($this->searchRole) {
            $query->where('role', $this->searchRole);
        }
        $users = $query->orderBy('id', 'desc')->paginate(8);
        return view('livewire.admin.user-list', compact('users'));
    }
}
