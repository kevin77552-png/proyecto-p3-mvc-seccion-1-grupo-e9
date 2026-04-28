<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\InventoryItem;
use Carbon\Carbon;

class InventoryConsulta extends Component
{
    public $search = '';
    public $search_pasillo = '';
    public $search_estante = '';
    public $exportFormat = 'csv'; // allow exporting current list

    public $showDeletionModal = false;
    public $deletionItemId = null;
    public $deletionReason = '';
    public $deletionQuantity = 0;
    public $deletionItemSigicov = '';
    public $deletionItemInventory = 0;

    protected $rules = [
        'deletionReason' => 'required|string|min:5',
        'deletionQuantity' => 'required|integer|min:1',
    ];

    /**
     * Mensajes de validación en español para la solicitud de eliminación.
     */
    protected $messages = [
        'deletionReason.required' => 'El motivo es obligatorio.',
        'deletionReason.string' => 'El motivo debe ser texto.',
        'deletionReason.min' => 'El motivo debe tener al menos :min caracteres.',
        'deletionQuantity.required' => 'La cantidad es obligatoria.',
        'deletionQuantity.integer' => 'La cantidad debe ser un número entero.',
        'deletionQuantity.min' => 'La cantidad debe ser al menos :min.',
    ];

    /**
     * Nombres de atributos legibles en español.
     */
    protected $validationAttributes = [
        'deletionReason' => 'motivo',
        'deletionQuantity' => 'cantidad',
    ];

    public function updated($propertyName)
    {
        // validación en tiempo real para campos del modal
        if (in_array($propertyName, ['deletionReason', 'deletionQuantity'])) {
            $this->validateOnly($propertyName);
        }
    }

    public function render()
    {
        $query = InventoryItem::query()
            ->when($this->search, fn($q) => $q->where('sigicov', 'like', '%'.$this->search.'%'))
            ->when($this->search_pasillo, fn($q) => $q->where('pasillo', 'like', '%'.$this->search_pasillo.'%'))
            ->when($this->search_estante, fn($q) => $q->where('estante', 'like', '%'.$this->search_estante.'%'))
            ->orderBy('id','desc');

        $items = $query->get();

        // compute monthly totals based on fecha (year-month)
        $totals = $items->groupBy(function ($it) {
            if ($it->fecha) {
                try {
                    return Carbon::parse($it->fecha)->format('Y-m');
                } catch (\Exception $e) {
                    return 'invalid';
                }
            }
            return 'none';
        })->map(fn($group) => $group->sum(fn($it) => (int) $it->inventario));

        // attach property to items for easy access in view/export
        foreach ($items as $it) {
            $key = 'none';
            if ($it->fecha) {
                try {
                    $key = Carbon::parse($it->fecha)->format('Y-m');
                } catch (\Exception $e) {
                    $key = 'invalid';
                }
            }
            $it->monthly_total = $totals[$key] ?? 0;
        }

        return view('livewire.admin.inventory-consulta', compact('items'));
    }

    public function openDeletionModal($itemId)
    {
        $this->deletionItemId = $itemId;
        $this->deletionReason = '';
        $this->deletionQuantity = 0;
        $this->deletionItemSigicov = '';
        $this->deletionItemInventory = 0;

        $item = InventoryItem::find($itemId);
        if ($item) {
            $this->deletionItemSigicov = $item->sigicov;
            $this->deletionItemInventory = (int) $item->inventario;
            $this->deletionQuantity = max(1, min($this->deletionItemInventory, 1));
            if ($this->deletionItemInventory > 0) $this->deletionQuantity = 1; else $this->deletionQuantity = 0;
        }

        $this->showDeletionModal = true;
    }

    public function export()
    {
        $format = strtolower($this->exportFormat) === 'xlsx' ? 'Xlsx' : 'Csv';

        // build same query as render so export respects filters
        $query = InventoryItem::query()
            ->when($this->search, fn($q) => $q->where('sigicov', 'like', '%'.$this->search.'%'))
            ->when($this->search_pasillo, fn($q) => $q->where('pasillo', 'like', '%'.$this->search_pasillo.'%'))
            ->when($this->search_estante, fn($q) => $q->where('estante', 'like', '%'.$this->search_estante.'%'))
            ->orderBy('id','desc');

        $items = $query->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers should match table columns
        $headers = ['SIGICOV','DESCRIPCIÓN','PASILLO','ESTANTE','PELDAÑO','FECHA','REALIZADO_POR','CANTIDAD_DISPONIBLE','RESP_ACCESORIOS','TS','TOTAL_MES'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $row = 2;
        foreach ($items as $it) {
            $sheet->setCellValue('A'.$row, $it->sigicov);
            $sheet->setCellValue('B'.$row, $it->descripcion);
            $sheet->setCellValue('C'.$row, $it->pasillo);
            $sheet->setCellValue('D'.$row, $it->estante);
            $sheet->setCellValue('E'.$row, $it->peldaño);
            $sheet->setCellValue('F'.$row, $it->fecha);
            $sheet->setCellValue('G'.$row, $it->realizado_por);
            $sheet->setCellValue('H'.$row, $it->inventario);
            $sheet->setCellValue('I'.$row, $it->resp_accesorios);
            $sheet->setCellValue('J'.$row, $it->created_at ? $it->created_at->format('Y-m-d H:i:s') : '');
            $sheet->setCellValue('K'.$row, $it->monthly_total ?? 0);
            $row++;
        }

        $writerClass = '\\PhpOffice\\PhpSpreadsheet\\Writer\\' . $format;
        $writer = new $writerClass($spreadsheet);

        $filename = 'inventario_export_'.date('Ymd_His').'.'.(strtolower($format) === 'xlsx' ? 'xlsx' : 'csv');

        $headers = [
            'Content-Type' => $format === 'Xlsx'
                ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                : 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, $headers);
    }

    public function submitDeletionRequest()
    {
        $this->validate([
            'deletionReason' => 'required|string|min:5',
            'deletionQuantity' => 'required|integer|min:1',
        ]);

        $item = InventoryItem::find($this->deletionItemId);
        if (!$item) {
            $this->addError('deletionReason', 'Repuesto no encontrado.');
            return;
        }

        if ($this->deletionQuantity < 1) {
            $this->addError('deletionQuantity', 'La cantidad debe ser al menos 1.');
            return;
        }

        if ($this->deletionQuantity > (int) $item->inventario) {
            $this->addError('deletionQuantity', 'La cantidad excede el inventario actual ('.$item->inventario.').');
            return;
        }

        // Evitar duplicados: si ya existe una solicitud de eliminación pendiente para este repuesto y usuario, no crear otra
        $existing = \App\Models\SpareRequest::where('tipo', 'eliminacion')
            ->where('inventory_item_id', $item->id)
            ->where('almacenista_id', auth()->id())
            ->whereIn('status', ['pendiente', 'in_progress', 'received'])
            ->first();

        if ($existing) {
            session()->flash('info', 'Ya existe una solicitud de eliminación pendiente para este repuesto.');
            $this->showDeletionModal = false;
            return;
        }

        $sr = \App\Models\SpareRequest::create([
            'cantidad' => $this->deletionQuantity,
            'descripcion' => 'Solicitud de eliminación de repuesto: '.substr($item->sigicov.' - '.$item->descripcion,0,150),
            'detalles' => $this->deletionReason,
            'tipo' => 'eliminacion',
            'inventory_item_id' => $item->id,
            'almacenista_id' => auth()->id(),
            'status' => 'pendiente',
        ]);

        \App\Models\Log::create([
            'accion' => 'solicitar_eliminacion',
            'entidad' => 'spare_request',
            'entidad_id' => $sr->id,
            'descripcion' => "inventory_item: '{$item->id}' => '{$item->sigicov} - {$item->descripcion}', cantidad: '{$this->deletionQuantity}', motivo: '{$this->deletionReason}'",
            'user_id' => auth()->id(),
        ]);

        $this->showDeletionModal = false;
        $this->deletionItemId = null;
        $this->deletionReason = '';
        $this->deletionQuantity = 0;
        $this->deletionItemSigicov = '';
        $this->deletionItemInventory = 0;

        session()->flash('success', 'Solicitud de eliminación enviada al gerente.');
        $this->dispatch('deletion-requested', ['message' => 'Solicitud de eliminación enviada al gerente.']);
    }
}
