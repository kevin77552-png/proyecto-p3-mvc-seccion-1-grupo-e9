<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use App\Models\User;
use App\Models\Log;

class SettingsRegistro extends Component
{
    public $max_administradores = 2;

    public function mount()
    {
        $this->max_administradores = min(12, (int) \App\Models\Setting::getValue('max_administradores', 2));
    }

    public function save()
    {
        // Enforce minimum of 2 and maximum of 12 administradores
        $input = (int) $this->max_administradores;
        $value = min(12, max(2, $input));

        $previous = (int) \App\Models\Setting::getValue('max_administradores', 2);
        \App\Models\Setting::setValue('max_administradores', (string)$value);

        // Log the configuration change
        Log::create([
            'accion' => 'configurar',
            'entidad' => 'setting',
            'entidad_id' => 0,
            'descripcion' => "Cambio max_administradores: {$previous} -> {$value}",
            'user_id' => auth()->id() ?? 0,
        ]);

        // Enforce limit: mantener activos los primeros N administradores por fecha de creación
        $admins = User::where('role', 'administrador')->orderBy('created_at', 'asc')->get();
        $keep = $value;
        $count = 0;
        foreach ($admins as $u) {
            $count++;
            if ($count <= $keep) {
                if (!$u->activo) {
                    $u->activo = true;
                    $u->save();
                    Log::create([
                        'accion' => 'habilitar',
                        'entidad' => 'user',
                        'entidad_id' => $u->id,
                        'descripcion' => 'Habilitado automáticamente por aumento de límite de administradores',
                        'user_id' => auth()->id() ?? 0,
                    ]);
                }
            } else {
                if ($u->activo) {
                    $u->activo = false;
                    $u->save();
                    Log::create([
                        'accion' => 'deshabilitar',
                        'entidad' => 'user',
                        'entidad_id' => $u->id,
                        'descripcion' => 'Deshabilitado automáticamente por reducción de límite de administradores',
                        'user_id' => auth()->id() ?? 0,
                    ]);
                }
            }
        }

        $this->max_administradores = $value;
        session()->flash('success', 'Configuración guardada.');
    }
    public function render()
    {
        $recentLogs = Log::whereIn('accion', ['habilitar', 'deshabilitar', 'configurar'])
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->limit(20)
            ->get();

        return view('livewire.admin.settings-registro', compact('recentLogs'));
    }
}
