<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MigrationImport;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Log;

class Migration extends Component
{
    public $exportFormat = 'csv';

    public function render()
    {
        return view('livewire.admin.migration');
    }

    /**
     * Export inventory in the selected format.
     * Returns a streamed CSV or an Excel download using Maatwebsite\Excel.
     */
    public function export()
    {
        $format = $this->exportFormat ?? 'csv';
        $filename = 'inventory_'.now()->format('Ymd_His').'.'.($format === 'xlsx' ? 'xlsx' : 'csv');

        if ($format === 'csv') {
            $callback = function () {
                $handle = fopen('php://output', 'w');
                // headings
                fputcsv($handle, ['item_number','sigicov','descripcion','unidad','inventario','resp_accesorios','pasillo','estante','peldaño','fecha','realizado_por']);

                InventoryItem::chunk(1000, function ($rows) use ($handle) {
                    foreach ($rows as $r) {
                        fputcsv($handle, [
                            $r->item_number,
                            $r->sigicov,
                            $r->descripcion,
                            $r->unidad,
                            $r->inventario,
                            $r->resp_accesorios,
                            $r->pasillo,
                            $r->estante,
                            $r->{'peldaño'},
                            $r->fecha,
                            $r->realizado_por,
                        ]);
                    }
                });

                fclose($handle);
            };

            return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
        }

        // XLSX export using a lightweight anonymous export class
        $export = new class implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function collection()
            {
                return InventoryItem::all()->map(function ($i) {
                    return [
                        'item_number' => $i->item_number,
                        'sigicov' => $i->sigicov,
                        'descripcion' => $i->descripcion,
                        'unidad' => $i->unidad,
                        'inventario' => $i->inventario,
                        'resp_accesorios' => $i->resp_accesorios,
                        'pasillo' => $i->pasillo,
                        'estante' => $i->estante,
                        'peldaño' => $i->{'peldaño'},
                        'fecha' => $i->fecha,
                        'realizado_por' => $i->realizado_por,
                    ];
                });
            }

            public function headings(): array
            {
                return ['item_number','sigicov','descripcion','unidad','inventario','resp_accesorios','pasillo','estante','peldaño','fecha','realizado_por'];
            }
        };

        return Excel::download($export, $filename);
    }





}
