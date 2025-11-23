<?php

namespace App\Livewire;

use App\Models\Merk;
use App\Models\MerkSparepart;
use App\Models\Sparepart;
use App\Models\TipeAC;
use App\Models\UnitAC;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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

    #[Url(except: 'all')]
    public $category = 'all';

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

    // Default price limits for Unit AC
    public $priceLimitMinUnit = 1_000_000; // 1 Juta
    public $priceLimitMaxUnit = 50_000_000; // 50 Juta

    // Price limits for Sparepart
    public $priceLimitMinSparepart = 1_000; // 1 Ribu
    public $priceLimitMaxSparepart = 10_000_000; // 10 Juta

    public $tempMinPrice;

    public $tempMaxPrice;

    protected $listeners = ['resetFilters' => 'resetFilters'];

    /** ─────────────────────────────
     *  Lifecycle Methods
     *  ───────────────────────────── */
    public function mount(): void
    {
        $this->minPrice ??= $this->getPriceLimitMin();
        $this->maxPrice ??= $this->getPriceLimitMax();

        $this->query = $this->searchTerm;
        $this->tempMinPrice = $this->minPrice;
        $this->tempMaxPrice = $this->maxPrice;
    }

    /** ─────────────────────────────
     *  Computed Properties
     *  ───────────────────────────── */
    public function getPriceLimitMin()
    {
        return $this->category === 'sparepart'
            ? $this->priceLimitMinSparepart
            : $this->priceLimitMinUnit;
    }

    public function getPriceLimitMax()
    {
        return $this->category === 'sparepart'
            ? $this->priceLimitMaxSparepart
            : $this->priceLimitMaxUnit;
    }

    /** ─────────────────────────────
     *  Reactive Handlers
     *  ───────────────────────────── */
    public function updated($property): void
    {
        // Reset filters and price range when category changes
        if ($property === 'category') {
            $this->tipe = null;
            $this->merk = null;

            // Reset price to new category limits
            $this->minPrice = $this->getPriceLimitMin();
            $this->maxPrice = $this->getPriceLimitMax();
            $this->tempMinPrice = $this->minPrice;
            $this->tempMaxPrice = $this->maxPrice;
        }

        if (in_array($property, ['tipe', 'merk', 'sortBy', 'category'])) {
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
            'searchTerm',
            'tipe',
            'merk',
            'sortBy',
            'category',
        ]);

        $this->minPrice = $this->getPriceLimitMin();
        $this->maxPrice = $this->getPriceLimitMax();
        $this->tempMinPrice = $this->minPrice;
        $this->tempMaxPrice = $this->maxPrice;

        $this->resetPage();
    }

    /** ─────────────────────────────
     *  Query Builder
     *  ───────────────────────────── */
    private function getFilteredProducts()
    {
        $term = '%' . $this->searchTerm . '%';

        // Query for UnitAC
        $units = UnitAC::query()
            ->select([
                'unit_ac.id',
                'unit_ac.nama_unit as name',
                DB::raw('COALESCE(NULLIF(unit_ac.harga_ecommerce, 0), unit_ac.harga_retail) as price'),
                'unit_ac.path_foto_produk as image_path',
                DB::raw("'unit' as type"),
                DB::raw('COALESCE(tipe_ac.tipe_ac, "Lainnya") as category'),
                'unit_ac.created_at',
                'unit_ac.stok_keluar',
            ])
            ->leftJoin('tipe_ac', 'unit_ac.tipe_ac_id', '=', 'tipe_ac.id')
            ->leftJoin('merk', 'unit_ac.merk_id', '=', 'merk.id')
            ->when($this->searchTerm, function (Builder $q) use ($term) {
                $q->where(function ($sub) use ($term) {
                    $sub->where('unit_ac.nama_unit', 'like', $term)
                        ->orWhere('unit_ac.sku', 'like', $term)
                        ->orWhere('unit_ac.keterangan', 'like', $term)
                        ->orWhere('merk.merk', 'like', $term)
                        ->orWhere('tipe_ac.tipe_ac', 'like', $term);
                });
            })
            ->when($this->tipe !== null, function ($q) {
                if ($this->tipe == 0) {
                    // Filter untuk "Lainnya" - unit yang tidak punya tipe_ac_id
                    $q->whereNull('unit_ac.tipe_ac_id');
                } else {
                    $q->where('unit_ac.tipe_ac_id', $this->tipe);
                }
            })
            ->when($this->merk !== null, function ($q) {
                if ($this->merk == 0) {
                    // Filter untuk "Lainnya" - unit yang tidak punya merk_id
                    $q->whereNull('unit_ac.merk_id');
                } else {
                    $q->where('unit_ac.merk_id', $this->merk);
                }
            })
            ->whereBetween(
                DB::raw('COALESCE(NULLIF(unit_ac.harga_ecommerce, 0), unit_ac.harga_retail)'),
                [$this->minPrice, $this->maxPrice]
            );

        // Query for Sparepart
        // Only include if no specific AC Type/Brand filter is applied, or handle logic if needed.
        // Assuming Spareparts shouldn't show if AC Type/Brand filters are active (as they don't match).
        $spareparts = Sparepart::query()
            ->select([
                'spareparts.id',
                'spareparts.nama_sparepart as name',
                'spareparts.harga_ecommerce as price',
                'spareparts.path_foto_sparepart as image_path',
                DB::raw("'sparepart' as type"),
                DB::raw("'Sparepart' as category"), // Placeholder category
                'spareparts.created_at',
                'spareparts.stok_keluar',
            ])
            ->leftJoin('merk_spareparts', 'spareparts.merk_spareparts_id', '=', 'merk_spareparts.id')
            ->when($this->searchTerm, function (Builder $q) use ($term) {
                $q->where(function ($sub) use ($term) {
                    $sub->where('spareparts.nama_sparepart', 'like', $term)
                        ->orWhere('spareparts.kode_sparepart', 'like', $term)
                        ->orWhere('spareparts.keterangan', 'like', $term)
                        ->orWhere('merk_spareparts.merk_spareparts', 'like', $term);
                });
            })
            ->when($this->merk !== null && $this->category === 'sparepart', function ($q) {
                if ($this->merk == 0) {
                    // Filter untuk "Lainnya" - sparepart yang tidak punya merk
                    $q->whereNull('spareparts.merk_spareparts_id');
                } else {
                    $q->where('spareparts.merk_spareparts_id', $this->merk);
                }
            })
            ->whereBetween('spareparts.harga_ecommerce', [$this->minPrice, $this->maxPrice]);

        // If filters for AC Type or AC Brand are active, we might want to exclude spareparts
        // because they don't belong to those AC Types/Brands (they have their own).
        if ($this->tipe || $this->merk || $this->category === 'unit') {
            // Return only units
            $query = $units;
        } elseif ($this->category === 'sparepart') {
            // Return only spareparts
            $query = $spareparts;
        } else {
            // Union both
            $query = $units->union($spareparts);
        }

        // Apply sorting to the combined query
        // Note: Union results can be ordered by wrapping or just ordering the final builder.
        // Laravel's union allows orderBy on the final query.

        return $query->orderBy(match ($this->sortBy) {
            'price_asc' => 'price',
            'price_desc' => 'price',
            'newest' => 'created_at',
            'name_asc' => 'name',
            'name_desc' => 'name',
            default => 'stok_keluar', // Default sort
        }, match ($this->sortBy) {
            'price_asc', 'name_asc' => 'asc',
            default => 'desc',
        })
            ->paginate($this->perPage);
    }

    /** ─────────────────────────────
     *  Helpers
     *  ───────────────────────────── */
    public function formatPrice($price): string
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    /** ─────────────────────────────
     *  Render
     *  ───────────────────────────── */
    public function render()
    {
        // Determine which brands to show based on category
        if ($this->category === 'sparepart') {
            $brands = MerkSparepart::whereHas('spareparts')
                ->select('id', 'merk_spareparts as merk')
                ->get();

            // Add "Lainnya" option for spareparts without merk
            $hasUnbranded = Sparepart::whereNull('merk_spareparts_id')->exists();
            if ($hasUnbranded) {
                $brands->prepend((object) ['id' => 0, 'merk' => 'Lainnya']);
            }
        } else {
            // For 'unit' or 'all' category, show AC unit brands
            $brands = Merk::whereHas('unitAC')
                ->select('id', 'merk')
                ->get();

            // Add "Lainnya" option for units without merk
            $hasUnbranded = UnitAC::whereNull('merk_id')->exists();
            if ($hasUnbranded) {
                $brands->prepend((object) ['id' => 0, 'merk' => 'Lainnya']);
            }
        }

        // Get types with "Lainnya" option
        $types = TipeAC::whereHas('unitAC')->select('id', 'tipe_ac')->get();

        // Add "Lainnya" option for units without tipe_ac
        $hasUntypedUnits = UnitAC::whereNull('tipe_ac_id')->exists();
        if ($hasUntypedUnits) {
            $types->prepend((object) ['id' => 0, 'tipe_ac' => 'Lainnya']);
        }

        return view('pages.products', [
            'products' => $this->getFilteredProducts(),
            'types' => $types,
            'brands' => $brands,
            'category' => $this->category,
            'priceLimitMin' => $this->getPriceLimitMin(),
            'priceLimitMax' => $this->getPriceLimitMax(),
        ])->extends('layouts.app');
    }
}
