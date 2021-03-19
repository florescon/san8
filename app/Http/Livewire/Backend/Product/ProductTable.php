<?php

namespace App\Http\Livewire\Backend\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Backend\DataTable\WithBulkActions;
use App\Http\Livewire\Backend\DataTable\WithCachedRows;
use Carbon\Carbon;

class ProductTable extends Component
{

	use Withpagination, WithBulkActions, WithCachedRows;

    protected $paginationTheme = 'bootstrap';

	protected $queryString = [
        'searchTerm' => ['except' => ''],
        'perPage',
    ];


    public $perPage = '12';

    public $status;
    public $searchTerm = '';

    protected $listeners = ['restore' => '$refresh'];


    public function getRowsQueryProperty()
    {
        
        $query = Product::query()
            ->with('children')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('code', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            })->where('parent_id', NULL)->orderBy('updated_at', 'desc');


        if ($this->status === 'deleted') {
            return $query->onlyTrashed();
        }

        return $query;


    }


    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->rowsQuery->paginate($this->perPage);
        });
    }



    public function clear()
    {
        $this->searchTerm = '';
        $this->page = 1;
        $this->perPage = '12';
    }


    public function updatedSearchTerm()
    {
        $this->page = 1;
    }

    public function updatedPerPage()
    {
        $this->page = 1;
    }



    public function restore($id)
    {
        if($id){
            $restore_product = Product::withTrashed()
                ->where('id', $id)
                ->restore();
        }

      $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Restored'), 
        ]);

    }

    public function render()
    {
        return view('backend.product.table.product-table', [
            'products' => $this->rows,
        ]);
    }




}
