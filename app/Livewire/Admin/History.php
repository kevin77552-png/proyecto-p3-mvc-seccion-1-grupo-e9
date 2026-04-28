<?php

namespace App\Livewire\Admin;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Log;
use App\Models\User;
use App\Models\InventoryItem;

class History extends Component
{
    use WithPagination;

    public $perPage = 15;
    public $entityFilter = '';
    public $userFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $paginationTheme = 'tailwind';

    public function updatingEntityFilter()
    {
        $this->resetPage();
    }

    public function updatingUserFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Log::with('user')->orderBy('created_at', 'desc');

        if ($this->entityFilter) {
            $query->where('entidad', $this->entityFilter);
        }

        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $logs = $query->paginate($this->perPage);

        // For filter selects
        $entities = Log::select('entidad')->distinct()->pluck('entidad');
        $users = User::select('id','name')->orderBy('name')->get();

        return view('livewire.admin.history', compact('logs','entities','users'));
    }
}
