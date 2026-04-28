<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\WithPagination;

class DynamicModule extends Component
{
    public $module = null;

    protected $listeners = ['showModule'];

    public function showModule($module)
    {
        $this->module = $module;
    }

    public function render()
    {
        return view('livewire.dynamic-module', [
            'module' => $this->module,
        ]);
    }
}
