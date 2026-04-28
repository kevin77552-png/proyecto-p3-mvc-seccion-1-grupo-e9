<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $selected = null;

    protected $listeners = ['showModule' => 'setSelected'];

    public function setSelected($module)
    {
        $this->selected = $module;
    }

    public function select($module)
    {
        $this->selected = $module;
        $this->dispatch('showModule', $module);
    }

    public function render()
    {
        return view('livewire.sidebar', [
            'selected' => $this->selected,
        ]);
    }
}
