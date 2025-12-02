<?php

namespace App\Livewire;

use App\Models\UnitAC;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';

    public $showResults = false;

    public function updatedQuery()
    {
        $this->showResults = strlen($this->query) > 0;
    }

    public function selectProduct($id)
    {
        return redirect()->to(\App\Helpers\PriceHelper::url('/produk/'.$id));
    }

    public function render()
    {
        $results = [];

        if ($this->showResults && strlen($this->query) > 0) {
            $results = UnitAC::with(['merk', 'tipeAC'])
                ->where('nama_unit', 'like', '%'.$this->query.'%')
                ->orWhereHas('merk', function ($q) {
                    $q->where('merk', 'like', '%'.$this->query.'%');
                })
                ->orWhereHas('tipeAC', function ($q) {
                    $q->where('tipe_ac', 'like', '%'.$this->query.'%');
                })
                ->take(5)
                ->get();
        }

        return view('livewire.global-search', [
            'results' => $results,
        ]);
    }
}
