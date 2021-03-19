<?php

namespace App\Http\Livewire\Backend\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class EditProduct extends Component
{

    use WithFileUploads;

    public $model, $shortId, $isCode, $newCode, $isDescription, $origDescription, $newDescription, $origName, $increaseStock, $subtractStock, $increaseStockRevision, $subtractStockRevision, $inputincrease, $inputsubtract, $inputincreaserevision, $inputsubtractrevision, $product_id, $color_id_select, $size_id_select, $update, $photo, $imageName, $origPhoto;

    public $colorsmultiple_id = [];
    public $sizesmultiple_id = [];


	protected $queryString = [
        'increaseStock' => ['except' => FALSE],
        'subtractStock' => ['except' => FALSE],
        'increaseStockRevision' => ['except' => FALSE],
        'subtractStockRevision' => ['except' => FALSE],

    ];

    protected $listeners = ['increase', 'savecolor', 'storemultiple', 'clearAll' => '$refresh'];

    public function mount(Product $product)
    {
        $this->product_id = $product->id;
        $this->shortId = $product->slug;
        $this->origPhoto = $product->file_name;
        $this->origDescription = $product->description;

    	$this->model = Product::with('children')
            ->findOrFail($product->id);

        $this->init($product);

    }

    public function removePhoto()
    {
        $this->photo = '';

    }


    public function save()
    {
        $product = Product::findOrFail($this->product_id);
        $newDescription = (string)Str::of($this->newDescription)->trim()->substr(0, 100); // trim whitespace & more than 100 characters
        $newDescription = $newDescription === $this->shortId ? null : $newDescription; // don't save it as product name it if it's identical to the short_id

        $product->description = $newDescription ?? null;
        $product->save();

        $this->init($product); // re-initialize the component state with fresh data after saving


        $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Updated at'), 
        ]);

    }

    public function savePhoto()
    {

        $this->validate([
            'photo' => 'image|max:4096', // 4MB Max
        ]);

        if($this->photo){
            $imageName = $this->photo->store("images",'public');
        }

        $record = Product::find($this->product_id);
        $record->update([
            'file_name' => $this->photo ? $imageName : null,
        ]);

        $this->removePhoto();

        $product = Product::findOrFail($this->product_id);
        $this->initphoto($product);

        $this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Saved photo'), 
        ]);


    }


    public function storemultiple()
    {

        $product = Product::findOrFail($this->product_id);

        $this->validate([
            'colorsmultiple_id' => 'required',
            'sizesmultiple_id' => 'required',
        ]);

        foreach($this->colorsmultiple_id as $color){
            
            foreach($this->sizesmultiple_id as $size){        
                $product->children()->saveMany([
                    new Product([
                        'size_id' => $size,
                        'color_id' => $color,
                    ]),
                ]);
            }
        }

        return redirect()->route('admin.product.edit', $product->id);

    }



    public function savecolor()
    {

        $product = Product::with('children')->findOrFail($this->product_id);


        if($this->color_id_select){
            if(!$product->children->contains('color_id', $this->color_id_select)){

                foreach($product->children->unique('size_id') as $sizes){
                    $product->children()->saveMany([
                        new Product([
                            'size_id' => $sizes->size->id,
                            'color_id' => $this->color_id_select,
                        ]),
                    ]);
                }


                $this->emit('swal:alert', [
                    'icon' => 'success',
                    'title'   => __('New color added'), 
                ]);
            }

            else{

                $this->emit('swal:alert', [
                    'icon' => 'warning',
                    'title'   => __('Color already exists'), 
                ]);

            }
        }

        $this->initmodel($product); // re-initialize the component state with fresh data after saving


    }


    public function savesize()
    {

        $product = Product::with('children')->findOrFail($this->product_id);

        // dd($this->size_id_select);

        if($this->size_id_select){

            if(!$product->children->contains('size_id', $this->size_id_select)){

                foreach($product->children->unique('color_id') as $colors){
                    $product->children()->saveMany([
                        new Product([
                            'size_id' => $this->size_id_select,
                            'color_id' => $colors->color->id,
                        ]),
                    ]);
                }

                $this->emit('swal:alert', [
                    'icon' => 'success',
                    'title'   => __('New size added'), 
                ]);

            }
            else{

                $this->emit('swal:alert', [
                    'icon' => 'warning',
                    'title'   => __('Size already exists'), 
                ]);
            }

        }

        $this->initmodel($product); // re-initialize the component state with fresh data after saving

    }

    // public function callIncreaseStock()
    // {
    //     $this->increaseStock;
    // }


    public function clearAll()
    {
        $this->inputincrease = [];
        $this->inputsubtract = [];

        $this->inputincreaserevision = [];
        $this->inputsubtractrevision = [];

    	$this->increaseStock = FALSE;
    	$this->subtractStock = FALSE;
        $this->increaseStockRevision = FALSE;
        $this->subtractStockRevision = FALSE;

    }


    public function increase($product_id)
    {


        // dd($this->inputsubtract);

        $this->validate([
            'inputincrease.*.stock' => 'numeric|sometimes',
            'inputsubtract.*.stock' => 'numeric|sometimes',
            'inputincreaserevision.*.stock' => 'numeric|sometimes',
            'inputsubtractrevision.*.stock' => 'numeric|sometimes',
        ]);

        if($this->inputincrease){

    		foreach($this->inputincrease as $key => $productos){
    			// dd($productos);
    			if(!empty($productos['stock']))
    			{
		            $product_increment = Product::where('id', $key)->first();
		            $product_increment->increment('stock', abs($productos['stock']));
    			}
    		}
    	}

        if($this->inputsubtract){

    		foreach($this->inputsubtract as $key => $productos){
    			// dd($productos);
    			if(!empty($productos['stock']))
    			{
		            $product_increment = Product::where('id', $key)->first();
		            $product_increment->decrement('stock', abs($productos['stock']));
    			}
    		}
    	}

        if($this->inputincreaserevision){

            foreach($this->inputincreaserevision as $key => $productos){
                // dd($productos);
                if(!empty($productos['stock']))
                {
                    $product_increment = Product::where('id', $key)->first();
                    $product_increment->increment('stock_revision', abs($productos['stock']));
                }
            }
        }

        if($this->inputsubtractrevision){

            foreach($this->inputsubtractrevision as $key => $productos){
                // dd($productos);
                if(!empty($productos['stock']))
                {
                    $product_increment = Product::where('id', $key)->first();
                    $product_increment->decrement('stock_revision', abs($productos['stock']));
                }
            }
        }


		$this->emit('clearAll');
		$this->clearAll();

       	$this->emit('swal:alert', [
            'icon' => 'success',
            'title'   => __('Increased'), 
        ]);

    }


    public function updatedSubtractStock()
    {
        $this->increaseStock = FALSE;
        $this->increaseStockRevision = FALSE;
        // $this->subtractStockRevision= FALSE;
    }

    public function updatedIncreaseStock()
    {
        $this->subtractStock = FALSE;
        // $this->increaseStockRevision = FALSE;
        $this->subtractStockRevision= FALSE;
    }


    public function updatedSubtractStockRevision()
    {
        $this->increaseStock = FALSE;
        // $this->subtractStock = FALSE;
        $this->increaseStockRevision = FALSE;
    }

    public function updatedIncreaseStockRevision()
    {
        // $this->increaseStock = FALSE;
        $this->subtractStock = FALSE;
        $this->subtractStockRevision= FALSE;
    }

    private function init(Product $product)
    {
        $this->origDescription = $product->description ?: $this->shortId;
        $this->newDescription = $this->origDescription;
        $this->isDescription = $product->description ?? false;

    }

    private function initphoto(Product $product)
    {
        $this->origPhoto = $product->file_name;
    }

    private function initmodel(Product $product)
    {
        $this->model = Product::with('children')
            ->findOrFail($product->id);
    }


    public function render()
    {
        return view('backend.product.livewire.edit');
    }

}
