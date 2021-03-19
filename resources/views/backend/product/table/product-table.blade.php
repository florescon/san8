{{-- <div class="card"> --}}


	<div class="card-body">

	  <div class="row mb-4 justify-content-md-center">
	    <div class="col-8">
	      <div class="input-group">
	        <input wire:model.debounce.350ms="searchTerm" class="form-control" type="text" placeholder="{{ __('Search') }}..." />
	        @if($searchTerm !== '')
	        <div class="input-group-append">
	          <button type="button" wire:click="clear" class="close" aria-label="Close">
	            <span aria-hidden="true"> &nbsp; &times; &nbsp;</span>
	          </button>

	        </div>
	        @endif
	      </div>
	    </div>
	  </div>


		<div class="card-columns">
			@foreach($products as $product)
			  <div class="card card-flyer">
			  	@if($product->file_name)
			  	{{-- @if(Storage::exists($product->file_name)) --}}
			    	<a href="{{ route('admin.product.edit', $product->id) }}">
				    	<img class="card-img-top" src="{{ asset('/storage/' . $product->file_name) }}" alt="{{ $product->name }}">
				    </a>
			    @endif
			    <div class="card-body" style="transform: rotate(0);">
			      <h5 class="card-title"><strong>{{ $product->name }}</strong></h5>
		          <h5 class="card-title text-muted">{{ $product->code }}</h5>

			      <p class="card-text">{!! $product->description_limited !!}</p>
			      <p class="card-text"><small class="text-muted">@lang('Last Updated') {{ $product->updated_at->diffForHumans() }}</small></p>
				<a href="{{ route('admin.product.edit', $product->id) }}" class="stretched-link"></a>
			    </div>

				  <ul class="list-group list-group-flush">
				    <li class="list-group-item">
				    	<strong>@lang('Colors'): </strong> 
				    	@foreach($product->children->unique('color_id') as $colors)
							<a href="#" class="badge badge-light">{{ $colors->color->name }}</a>
				    	@endforeach
				    </li>
				    <li class="list-group-item">
				    	<strong>@lang('Sizes'): </strong> 
				    	@foreach($product->children->unique('size_id') as $sizes)
							<a href="#" class="badge badge-light">{{ $sizes->size->name }}</a>
				    	@endforeach
				    </li>

				    <li class="list-group-item">
				    	<strong>@lang('Stock'): </strong> {{ $product->getTotalStock() }} 
				    </li>

				  </ul>

			    <div class="card-footer">
					@if (!$product->trashed())
						<a href="{{ route('admin.product.index') }}" class="btn btn-warning text-white">@lang('Consumption')</a>
						<a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-primary">@lang('Edit product')</a>
					@else
					    <div class="dropright" style="display:inline-block;">
					      <a class="btn btn-icon-only" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					          <i class="fas fa-ellipsis-v"></i>
					      </a>
					      <div class="dropdown-menu ">
					        <a class="dropdown-item" href="#" wire:click="restore({{ $product->id }})">
					          @lang('Restore')
					        </a>
					      </div>
					    </div>
					    <br>
					    <br>
				    @endif
				</div>
			  </div>
			@endforeach
		</div>


		  <div class="row mt-4">
		    <div class="col">
		        @if($products->count())
		        <div class="row">
		          <div class="col">
		            <nav>
		              {{ $products->links() }}
		            </nav>
		          </div>
		              <div class="col-sm-3 text-muted text-right">
		                Mostrando {{ $products->firstItem() }} - {{ $products->lastItem() }} de {{ $products->total() }} resultados
		              </div>
		        </div>

		        @else
		          @lang('No search results') 
		          @if($searchTerm)
		            "{{ $searchTerm }}" 
		          @endif

		          @if($page > 1)
		            {{ __('in the page').' '.$page }}
		          @endif
		        @endif

		    </div>
		  </div>

	</div>

 {{-- </div> --}}