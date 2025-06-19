<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Aset;

class AssetTable extends Component
{
    public $assets;

    public function mount()
    {
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $this->assets = Aset::with('projectRequests')->get();
    }

    public function markUnavailable($assetId)
    {
        $asset = Aset::find($assetId);
        if ($asset && $asset->status !== 'unavailable') {
            $asset->status = 'unavailable';
            $asset->save();
            $this->loadAssets();
            $this->emit('notify', 'Aset berhasil diubah menjadi unavailable.');
        }
    }

    public function render()
    {
        return view('livewire.asset-table');
    }
}
