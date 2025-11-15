<?php

namespace App\Livewire;

use App\Models\Merk;
use App\Models\TipeAC;
use App\Models\UnitAC;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public $searchTerm = '';

    #[Url(except: null)]
    public $tipe = null;

    #[Url(except: null)]
    public $merk = null;

    #[Url(except: null)]
    public $minPrice;

    #[Url(except: null)]
    public $maxPrice;

    #[Url(except: 'default')]
    public $sortBy = 'default';

    public $query = '';

    public $perPage = 12;

    public $priceLimitMin = 1_000_000; // 1 Juta

    public $priceLimitMax = 50_000_000; // 50 Juta

    public $tempMinPrice;

    public $tempMaxPrice;

    protected $listeners = ['resetFilters' => 'resetFilters'];

    /** ─────────────────────────────
     *  Lifecycle Methods
     *  ───────────────────────────── */
    public function mount(): void
    {
        $this->minPrice ??= $this->priceLimitMin;
        $this->maxPrice ??= $this->priceLimitMax;

        $this->query = $this->searchTerm;
        $this->tempMinPrice = $this->minPrice;
        $this->tempMaxPrice = $this->maxPrice;
    }

    /** ─────────────────────────────
     *  Reactive Handlers
     *  ───────────────────────────── */
    public function updated($property): void
    {
        if (in_array($property, ['tipe', 'merk', 'sortBy'])) {
            $this->resetPage();
        }
    }

    /** ─────────────────────────────
     *  Actions
     *  ───────────────────────────── */
    public function doSearch(): void
    {
        $this->searchTerm = $this->query;
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->minPrice = $this->tempMinPrice;
        $this->maxPrice = $this->tempMaxPrice;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'query',
            'searchTerm',
            'tipe',
            'merk',
            'sortBy',
        ]);

        $this->minPrice = $this->priceLimitMin;
        $this->maxPrice = $this->priceLimitMax;
        $this->tempMinPrice = $this->priceLimitMin;
        $this->tempMaxPrice = $this->priceLimitMax;

        $this->resetPage();
    }

    /** ─────────────────────────────
     *  Query Builder
     *  ───────────────────────────── */
    private function getFilteredProducts()
    {
        $term = '%'.$this->searchTerm.'%';

        return UnitAC::query()
            ->selectRaw('
            unit_ac.*,
            COALESCE(NULLIF(unit_ac.harga_ecommerce, 0), unit_ac.harga_retail) AS display_price,
            COALESCE(SUM(transaksi_produk_detail.jumlah_keluar), 0) AS total_sold
        ')
            ->leftJoin('transaksi_produk_detail', 'unit_ac.id', '=', 'transaksi_produk_detail.unit_ac_id')
            ->leftJoin('transaksi_produk', 'transaksi_produk.id', '=', 'transaksi_produk_detail.transaksi_produk_id')
            ->with(['tipeAC:id,tipe_ac', 'merk:id,merk'])
            ->when($this->searchTerm, function (Builder $q) use ($term) {
                $q->where(function ($sub) use ($term) {
                    $sub->where('unit_ac.nama_unit', 'like', $term)
                        ->orWhere('unit_ac.sku', 'like', $term)
                        ->orWhere('unit_ac.keterangan', 'like', $term)
                        ->orWhereHas('merk', fn ($m) => $m->where('merk', 'like', $term))
                        ->orWhereHas('tipeAC', fn ($t) => $t->where('tipe_ac', 'like', $term));
                });
            })
            ->when($this->tipe, fn ($q) => $q->where('unit_ac.tipe_ac_id', $this->tipe))
            ->when($this->merk, fn ($q) => $q->where('unit_ac.merk_id', $this->merk))
            ->whereBetween(
                \DB::raw('COALESCE(NULLIF(unit_ac.harga_ecommerce, 0), unit_ac.harga_retail)'),
                [$this->minPrice, $this->maxPrice]
            )
            ->groupBy('unit_ac.id')
            ->when($this->sortBy, function ($q) {
                return match ($this->sortBy) {
                    'price_asc' => $q->orderBy('display_price', 'asc'),
                    'price_desc' => $q->orderBy('display_price', 'desc'),
                    'newest' => $q->orderByDesc('unit_ac.created_at'),
                    'name_asc' => $q->orderBy('unit_ac.nama_unit'),
                    'name_desc' => $q->orderByDesc('unit_ac.nama_unit'),
                    default => $q->orderByDesc('total_sold')
                        ->orderByDesc('unit_ac.created_at'),
                };
            })
            ->paginate($this->perPage);
    }

    /** ─────────────────────────────
     *  Helpers
     *  ───────────────────────────── */
    public function formatPrice($price): string
    {
        return 'Rp '.number_format($price, 0, ',', '.');
    }

    /** ─────────────────────────────
     *  Render
     *  ───────────────────────────── */
    public function render()
    {
        return view('pages.products', [
            'products' => $this->getFilteredProducts(),
            'types' => TipeAC::select('id', 'tipe_ac')->get(),
            'brands' => Merk::select('id', 'merk')->get(),
        ])->extends('layouts.app');
    }
}
