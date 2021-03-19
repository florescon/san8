<?php

namespace App\Http\Livewire\Backend;

use App\Models\Color;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Backend\DataTable\WithBulkActions;
use App\Http\Livewire\Backend\DataTable\WithCachedRows;
use Carbon\Carbon;

class ColorTable extends Component
{

	use Withpagination, WithBulkActions, WithCachedRows;

    protected $paginationTheme = 'bootstrap';

	protected $queryString = [
        'searchTerm' => ['except' => ''],
        'perPage',
        'deleted' => ['except' => FALSE],
        'dateInput' => ['except' => ''],
        'dateOutput' => ['except' => '']
    ];


    public $perPage = '10';


    public $sortField = 'name';
    public $sortAsc = true;
    
    public $searchTerm = '';

    public $dateInput = '';
    public $dateOutput = '';

    protected $listeners = ['delete', 'restore' => '$refresh'];

    public $name, $color, $created, $updated, $selected_id, $deleted;

    protected $rules = [
        'name' => 'required|min:3',
        'color' => 'required|unique:colors',
    ];



    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }


    public function getRowsQueryProperty()
    {
        
        return Color::query()
            ->when($this->dateInput, function ($query) {
            empty($this->dateOutput) ?
            $query->whereBetween('updated_at', [$this->dateInput.' 00:00:00', now()]) :
            $query->whereBetween('updated_at', [$this->dateInput.' 00:00:00', $this->dateOutput.' 23:59:59']);
            })
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('color', 'like', '%' . $this->searchTerm . '%');
            })
            ->when($this->deleted, function ($query) {
                $query->onlyTrashed();
            })
            ->when($this->sortField, function ($query) {
                $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');
            });
    }


    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->rowsQuery->paginate($this->perPage);
        });
    }


    public function render()
    {
        return view('backend.color.table.color-table', [
            'colors' => $this->rows,
        ]);
    }

    public function clearFilterDate()
    {
        $this->dateInput = '';
        $this->dateOutput = '';
    }

    public function clearAll()
    {
        $this->dateInput = '';
        $this->dateOutput = '';
        $this->searchTerm = '';
        $this->page = 1;
        $this->perPage = '10';
        $this->deleted = FALSE;
        $this->selectAll = false;
        $this->selectPage = false;
        $this->selected = [];
 

    }


    public function clear()
    {
        $this->searchTerm = '';
        $this->page = 1;
        $this->perPage = '10';
    }

    public function updatedSearchTerm()
    {
        $this->page = 1;
    }

    public function updatedPerPage()
    {
        $this->page = 1;
    }

    public function updatedDeleted()
    {
        $this->page = 1;
        $this->selectAll = false;
        $this->selectPage = false;
        $this->selected = [];
    }


    public function createmodal()
    {
        $this->resetInputFields();

    }

    public function store()
    {

        $validatedData = $this->validate();


        Color::create($validatedData);

        $this->resetInputFields();
        $this->emit('colorStore');


       $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Created'), 
        ]);

        // session()->flash('message-success', __('The color was successfully created.'));

    }


    public function edit($id)
    {
        $record = Color::findOrFail($id);

        $this->selected_id = $id;
        $this->name = $record->name;
        $this->color = $record->color;


    }


    public function show($id)
    {
        $record = Color::withTrashed()->findOrFail($id);
        $this->name = $record->name;
        $this->color = $record->color;
        $this->created = $record->created_at;
        $this->updated = $record->updated_at;

    }


    public function update()
    {
        $this->validate([
            'selected_id' => 'required|numeric',
            'name' => 'required|min:3',
            'color' => 'required'
        ]);
        if ($this->selected_id) {
            $record = Color::find($this->selected_id);
            $record->update([
                'name' => $this->name,
                'color' => $this->color
            ]);
            $this->resetInputFields();
        }

        $this->emit('colorUpdate');

       $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Actualizado'), 
        ]);

    }

    private function resetInputFields(){

        $this->name = '';
        $this->color = '';

    }

    public function export()
    {
        return response()->streamDownload(function () {
            echo $this->selectedRowsQuery->toCsv();
        }, 'color-list.csv');
    }

    public function delete($id)
    {

        if($id){
            $color = Color::where('id', $id);
            $color->delete();

        }

       $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Deleted'), 
        ]);

    }

    public function restore($id)
    {
        if($id){
            $restore_color = Color::withTrashed()
                ->where('id', $id)
                ->restore();
        }

      $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Restored'), 
        ]);

    }


}
