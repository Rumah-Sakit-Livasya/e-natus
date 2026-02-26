<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Aset;

class ProjectAssetTable extends Component
{
    public $assetIds = [];
    public $projectRequestId;
    public $search = '';
    public $swapTargetId = null;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function render()
    {
        $assets = Aset::whereIn('id', $this->assetIds)->get();

        $availableAssets = [];
        if ($this->swapTargetId) {
            $query = Aset::where('status', 'available');
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('custom_name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            }
            $availableAssets = $query->limit(10)->get();
        }

        return view('livewire.project-asset-table', compact('assets', 'availableAssets'));
    }

    public function markUnavailable($assetId)
    {
        $asset = Aset::find($assetId);
        if ($asset && $asset->status !== 'unavailable') {
            $asset->status = 'unavailable';
            $asset->save();
            $this->assetIds = $this->assetIds;
        }
    }

    public function markAvailable($assetId)
    {
        $asset = Aset::find($assetId);
        if ($asset && $asset->status !== 'available') {
            $asset->status = 'available';
            $asset->save();

            // Opsional: Jika ingin menghapus dari list project juga saat dikembalikan secara manual dari modal
            // Tapi user minta "Hilangkan status dipakai tapi tidak merubah status bahwa aset pernah dipakai"
            // Jadi kita biarkan ID nya tetap ada di projectRequest agar history tetap ada?
            // Namun jika ID tetap ada, modal akan terus menampilkan aset tersebut.
            // Keputusannya: Biarkan di list modal tapi statusnya "Tersedia".
            $this->assetIds = $this->assetIds;
        }
    }

    public function initiateSwap($assetId)
    {
        $this->swapTargetId = $assetId;
        $this->search = '';
    }

    public function cancelSwap()
    {
        $this->swapTargetId = null;
    }

    public function swapAsset($oldAssetId, $newAssetId)
    {
        $oldAsset = Aset::find($oldAssetId);
        $newAsset = Aset::find($newAssetId);
        $project = \App\Models\ProjectRequest::find($this->projectRequestId);

        if ($oldAsset && $newAsset && $project) {
            // Update statuses
            $oldAsset->status = 'available';
            $oldAsset->save();

            $newAsset->status = 'unavailable';
            $newAsset->save();

            // Update project asset_ids JSON
            $ids = $project->asset_ids ?? [];
            if (($key = array_search($oldAssetId, $ids)) !== false) {
                $ids[$key] = (int) $newAssetId;
            } else {
                $ids[] = (int) $newAssetId;
            }
            $project->asset_ids = array_values(array_unique($ids));
            $project->save();

            $this->assetIds = $project->asset_ids;
            $this->swapTargetId = null;

            \Filament\Notifications\Notification::make()
                ->title('Aset Berhasil Ditukar')
                ->success()
                ->send();
        }
    }
}
