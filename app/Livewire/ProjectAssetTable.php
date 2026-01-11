<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Aset;

class ProjectAssetTable extends Component
{
    public $assetIds = [];

    public function render()
    {
        $assets = Aset::whereIn('id', $this->assetIds)->get();

        return view('livewire.project-asset-table', compact('assets'));
    }

    public function markUnavailable($assetId)
    {
        $asset = Aset::find($assetId);
        if ($asset && $asset->status !== 'unavailable') {
            $asset->status = 'unavailable';
            $asset->save();

            // Refresh aset list after update
            $this->assetIds = $this->assetIds; // trigger re-render, sebenarnya Livewire otomatis re-render
        }
    }
}
