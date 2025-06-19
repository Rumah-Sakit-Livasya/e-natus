<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProcurementItem;

class ProcurementItemsTable extends Component
{
    public array $items = [];
    public ?int $procurementId = null;

    public function mount(?int $procurementId = null)
    {
        $this->procurementId = $procurementId;
        $this->loadItems();
    }

    public function loadItems()
    {
        $this->items = ProcurementItem::where('procurement_id', $this->procurementId)
            ->get()
            ->map(fn($item) => $item->toArray())
            ->toArray();
    }

    public function render()
    {
        return view('livewire.procurement-items-table', ['items' => $this->items]);
    }

    public function markTerealisasi(int $itemId)
    {
        ProcurementItem::where('id', $itemId)->update(['status' => 'Terealisasi']);
        $this->updateItemStatus($itemId, 'Terealisasi');
    }

    public function markTidakTerealisasi(int $itemId)
    {
        ProcurementItem::where('id', $itemId)->update(['status' => 'Tidak Terealisasi']);
        $this->updateItemStatus($itemId, 'Tidak Terealisasi');
    }

    private function updateItemStatus(int $itemId, string $status)
    {
        $this->items = collect($this->items)->map(function ($item) use ($itemId, $status) {
            if ($item['id'] === $itemId) {
                $item['status'] = $status;
            }
            return $item;
        })->values()->toArray();
    }
}
