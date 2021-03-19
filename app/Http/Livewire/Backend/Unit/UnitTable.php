<?php

namespace App\Http\Livewire\Backend\Unit;

use App\Models\Unit;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\TableComponent;
use Rappasoft\LaravelLivewireTables\Traits\HtmlComponents;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UnitTable extends TableComponent
{
    use HtmlComponents;

    use WithPagination;


    public $search;

    public $status;

    public $perPage = '10';

    public $tableFooterEnabled = true;

    public $perPageOptions = ['10', '25', '50', '100'];

    public $exports = ['csv', 'xls', 'xlsx'];
    public $exportFileName = 'units';

    public $clearSearchButton = true;
    
    protected $queryString = [
        'search' => ['except' => ''], 
        'perPage',

    ];

    protected $listeners = ['delete', 'restore', 'triggerRefresh' => '$refresh'];


    /**
     * @var string
     */
    public $sortField = 'updated_at';

    public $sortDirection = 'desc';



    /**
     * @var array
     */
    protected $options = [
        'bootstrap.container' => false,
        'bootstrap.classes.table' => 'table table-striped table-bordered',
        'bootstrap.responsive' => true,

    ];


    /**
     * @param  string  $status
     */
    public function mount($status = 'active'): void
    {
        $this->status = $status;
    }


    /**
     * @return Builder
     */
    public function query(): Builder
    {

        $query = Unit::query();

        if ($this->status === 'deleted') {
            return $query->onlyTrashed();
        }

        return $query;
		// return unit::query()
        // ->when($this->deleted, function ($query) {
            // $query->onlyTrashed();
        // });
    }


    /**
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::make(__('Name'), 'name')
                ->searchable()
                ->sortable(),
            Column::make(__('Slug'), 'slug')
                ->searchable()
                ->sortable()
                ->format(function(Unit $model) {
                    return $this->html($model->slug ? $model->slug : '<span class="badge badge-secondary">'.__('undefined').'</span>');
                })
                ->exportFormat(function(Unit $model) {
                    return $model->slug;
                }),
            Column::make(__('Created at'), 'created_at')
                ->searchable()
                ->sortable(),
            Column::make(__('Updated at'), 'updated_at')
                ->searchable()
                ->sortable(),
            Column::make(__('Actions'))
                ->format(function (Unit $model) {
                    return view('backend.unit.datatable.actions', ['unit' => $model]);
                }),
        ];
    }

    public function delete($id)
    {

        if($id){
            $unit = Unit::where('id', $id);
            $unit->delete();

        }

       $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Deleted'), 
        ]);

    }

    public function restore($id)
    {
        if($id){
            $restore_unit = Unit::withTrashed()
                ->where('id', $id)
                ->restore();
        }

      $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Restored'), 
        ]);

    }

}
