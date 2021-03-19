<x-backend.card>

	@json($model);
	<x-slot name="header">
        @lang('Update product')
 	</x-slot>

    <x-slot name="headerActions">
        <x-utils.link class="card-header-action" :href="route('admin.product.index')" :text="__('Cancel')" />
 	</x-slot>
    <x-slot name="body">

		<div class="row ">

			<div class="col-12 col-md-4">

			    <div class="card card-flyer">

	                @if ($photo)
	                    @php
	                        try {
	                           $url = $photo->temporaryUrl();
	                           $photoStatus = true;
	                        }catch (RuntimeException $exception){
	                            $this->photoStatus =  false;
	                        }
	                    @endphp
	                    @if($photoStatus)
	                        <img class="card-img-top" alt="Responsive image" src="{{ $url }}">
	                        <br>
						  <ul class="list-group list-group-flush">
						    <li class="list-group-item">
		                        <div wire:loading.remove wire:target="photo"> 
								    <a href="#" wire:click="removePhoto" class="card-link">Cancelar</a>
								    <a href="#" wire:click="savePhoto" class="card-link pulsingButton">@lang('Save photo')</a>
								</div>
						    </li>
						  </ul>

	                    @else
	                        @lang('Something went wrong while uploading the file.')
	                    @endif
                    @else
                    	@if($origPhoto)
			  	    	<img class="card-img-top" src="{{ asset('/storage/' . $origPhoto) }}" alt="Card image cap">
			  	    	@endif
				    @endif



					  <ul class="list-group list-group-flush">
					    <li class="list-group-item">

                        <div wire:loading wire:target="photo">@lang('Uploading')...</div>

							<div class="custom-file">
							  <input type="file" wire:model.lazy="photo" class="custom-file-input" id="customFile">
							  <label class="custom-file-label" for="customFile" data-browse="@lang('Select')">@lang('New photo')</label>
							</div>

					    </li>
					  </ul>

			      <div class="card-body">
				    <h5 class="card-title"><strong>{{ $model->name }}</strong></h5>

				    <div
				        x-data="
				            {
				                 isEditing: false,
				                 isCode: '{{ $model->code }}',
				                 focus: function() {
				                    const textInput = this.$refs.textInput;
				                    textInput.focus();
				                    textInput.select();
				                 }
				            }
				        "
				        x-cloak
				    >

			            <div
				            x-show=!isEditing
				        >
					        <h5 class="card-title text-muted"
				                x-bind:class="{ 'font-weight-bold': isCode }"
				                x-on:click="isEditing = true; $nextTick(() => focus())"
					       		style="border-bottom: 1px dashed black;" >
				       			{{ $model->code }}
					    	</h5>
					    </div>


				        <div x-show=isEditing >
				            <form class="flex" wire:submit.prevent="save">

								<div class="input-group">
						        	<input type="text" class="form-control" 
						        	wire:model.lazy="newCode"
				                    x-ref="textInput"
				                    x-on:keydown.escape="isEditing = false"
						        	>
								  <div class="input-group-append">
								    <span class="input-group-text" x-on:click="isEditing = false">
								    	<i class="cil-x"></i>
								    </span>
	
								 	<button class="btn btn-primary"  x-on:click="isEditing = false" type="submit"><i class="cil-check-alt"></i></button>

								  </div>
								</div>
				    		</form>
				            <small class="text-xs">@lang('Enter to save, Esc to cancel')</small>
				        </div>

					</div>

				    <div
				        x-data="
				            {
				                 isEditing: false,
				                 isDescription: '{{ $isDescription }}',
				                 focus: function() {
				                    const textInput = this.$refs.textInput;
				                    textInput.focus();
				                    textInput.select();
				                 }
				            }
				        "
				        x-cloak
				    >
			            <div
				            x-show=!isEditing
				        >
					        <p  class="card-text" 
				                x-bind:class="{ 'font-weight-bold': isDescription }"
				                x-on:click="isEditing = true; $nextTick(() => focus())"
					       		style="border-bottom: 1px dashed black;" 
					        >{{ $origDescription }}</p>
					    </div>

				        <div x-show=isEditing >
				            <form class="flex" wire:submit.prevent="save">

								<div class="input-group">
						        	<input type="text" class="form-control" 
						        	wire:model.lazy="newDescription"
				                    x-ref="textInput"
				                    x-on:keydown.escape="isEditing = false"
						        	>
								  <div class="input-group-append">
								    <span class="input-group-text" x-on:click="isEditing = false">
								    	<i class="cil-x"></i>
								    </span>
	
								 	<button class="btn btn-primary"  x-on:click="isEditing = false" type="submit"><i class="cil-check-alt"></i></button>

								  </div>
								</div>
				    		</form>
				            <small class="text-xs">@lang('Enter to save, Esc to cancel')</small>
				        </div>
				    </div>

				    <br>

			        <p class="card-text"><strong>@lang('Total stock'): </strong>{{ $model->getTotalStock() }}</p>
			        <p class="card-text"><strong>@lang('Updated at'): </strong>{{ $model->updated_at }}</p>
			        <p class="card-text"><strong>@lang('Created at'): </strong>{{ $model->created_at }}</p>

			        {{-- <a href="#" class="btn btn-primary pulsingButton">@lang('Edit')</a> --}}
			        {{-- <a href="#" class="btn btn-primary ">@lang('Edit')</a> --}}
			      </div>
			    </div>
			</div>

  			<div class="col-12 col-sm-6 col-md-8">

				@if(!$model->children->count())
				<form wire:submit.prevent="storemultiple">

	                <div class="form-group row" wire:ignore>
	                    <label for="colorselectmultiple" class="col-sm-2 col-form-label">@lang('Colors')</label>

	                    <div class="col-sm-10" >
	                        <select id="colorselectmultiple" multiple="multiple" class="custom-select" style="width: 100%;" aria-hidden="true" >
	                        </select>
	                    </div>
	                </div><!--form-group-->


	                <div class="form-group row" wire:ignore>
	                    <label for="sizeselectmuliple" class="col-sm-2 col-form-label">@lang('Sizes')</label>

	                    <div class="col-sm-10" >
	                        <select id="sizeselectmuliple" multiple="multiple" class="custom-select" style="width: 100%;" aria-hidden="true">
	                        </select>
	                    </div>
	                </div><!--form-group-->
	                @if($colorsmultiple_id && $sizesmultiple_id)
	                	<button class="btn btn-sm btn-primary float-right" type="submit">@lang('Save')</button>
	                @endif

	            </form>
                @else 

  				<div class="row">
	  				<div class="col-12">
	  					<h5> @lang('Colors'): 
	
						    <div style="display:inline-block;" 
						        x-data="
						            {
						                 isNewColor: false,
						            }
						        "
						        x-cloak
						    >
					            <div
						            x-show=!isNewColor
						        >
									<span class="badge bg-success text-white" x-on:click="isNewColor = true; $nextTick(() => focus())"> <i class="cil-plus"></i> </span>

							    </div>



						        <div x-show=isNewColor >
						            <form class="flex" wire:submit.prevent="savecolor">

										<div class="input-group w-80">
									    	<div wire:ignore x-on:keydown.escape="isNewColor = false">
								     			<select  id="colorselect"  class="custom-select" aria-hidden="true" required>
								        		</select>
								    		</div>

									    	<div class="input-group-append">
											    <span class="input-group-text" x-on:click="isNewColor = false">
											    	<i class="cil-x"></i>
											    </span>
			
										 		<button class="btn btn-primary"  x-on:click="isNewColor = false" type="submit"><i class="cil-check-alt"></i></button>

										  	</div>
										</div>
						    		</form>
						        </div>
						    </div>

	  						@foreach($model->children->unique('color_id') as $children) 	
								<span class="badge bg-secondary">{{ $children->color->name }}</span>
							@endforeach
						</h5>
					</div>


	  				<div class="col-12">
	  					<h5> @lang('Sizes'):
				    <div style="display:inline-block;" 
				        x-data="
				            {
				                 isNewSize: false,
				            }
				        "
				        x-cloak
				    >
			            <div
				            x-show=!isNewSize
				        >
							<span class="badge bg-success text-white" x-on:click="isNewSize = true; $nextTick(() => focus())"> <i class="cil-plus"></i> </span>

					    </div>



				        <div x-show=isNewSize >
				            <form class="flex" wire:submit.prevent="savesize">

								<div class="input-group w-80">
							    	<div wire:ignore x-on:keydown.escape="isNewSize = false">
						     			<select  id="sizeselect"  class="custom-select" aria-hidden="true" required>
						        		</select>
						    		</div>

							    	<div class="input-group-append">
									    <span class="input-group-text" x-on:click="isNewSize = false">
									    	<i class="cil-x"></i>
									    </span>
	
								 		<button class="btn btn-primary"  x-on:click="isNewSize = false" type="submit"><i class="cil-check-alt"></i></button>

								  	</div>
								</div>
				    		</form>
				        </div>
				    </div>

	  						@foreach($model->children->unique('size_id') as $children) 	
								<span class="badge bg-secondary">{{ $children->size->name }}</span>
							@endforeach								
						</h5>
					</div>
				</div>

				@endif

				<br>

				@if($model->children->count())
  				<div class="row">
	  				<div class="col-9">

						<div class="custom-control custom-switch custom-control-inline">
							<input type="checkbox" wire:model="increaseStock" id="customRadioInline1" name="customRadioInline1" class="custom-control-input">
							<label class="custom-control-label" for="customRadioInline1"><p class="{{ $increaseStock ? 'text-primary' : 'text-dark' }}"> @lang('Increase')</p></label>
						</div>
						<div class="custom-control custom-switch custom-control-inline">
							<input type="checkbox" wire:model="subtractStock" id="customRadioInline2" name="customRadioInline2" class="custom-control-input">
							<label class="custom-control-label" for="customRadioInline2"><p class="{{ $subtractStock ? 'text-danger' : 'text-dark' }}"> @lang('Subtract')</p></label>
						</div>
					
					    <div class="w-100 d-none d-md-block"></div>

						<div class="custom-control custom-switch custom-control-inline">
							<input type="checkbox" wire:model="increaseStockRevision" id="customRadioInline3" name="customRadioInline3" class="custom-control-input">
							<label class="custom-control-label" for="customRadioInline3"><p class="{{ $increaseStockRevision ? 'text-primary' : 'text-dark' }}"> @lang('Increase') S.R.I</p></label>
						</div>
						<div class="custom-control custom-switch custom-control-inline">
							<input type="checkbox" wire:model="subtractStockRevision" id="customRadioInline4" name="customRadioInline4" class="custom-control-input">
							<label class="custom-control-label" for="customRadioInline4"><p class="{{ $subtractStockRevision ? 'text-danger' : 'text-dark' }}"> @lang('Subtract') S.R.I</p></label>
						</div>

					</div>

	  				<div class="col-3">

			            <button type="button" class="btn btn-primary btn-sm float-right" wire:click="clearAll" class="btn btn-default">@lang('Clear filters')</button>

			        </div>

		        </div>
				@endif

				<br>

				@if($model->children->count())

				  	@foreach($model->children->groupBy('color_id') as $childrens)

				  	<strong>{{ $childrens->first()->color->name }}</strong>

				    <div class="card" style="{{ $childrens->first()->color->color ? 'border: '. $childrens->first()->color->color. ' 3px solid' : '' }} ">
				      <div class="card-body">
						<div class="table-responsive">
						<table class="table table-sm">
						  <thead>
						    <tr>
						      <th scope="col">#</th>
						      <th scope="col">@lang('Color')</th>
						      <th scope="col">@lang('Size_')</th>
						      <th scope="col">@lang('Stock')</th>
						      <th scope="col">@lang('Revision stock')</th>
						      <th scope="col">@lang('Store stock')</th>
						      @if($increaseStock == TRUE)
							      <th scope="col">@lang('Increase')</th>
						      @endif
						      @if($increaseStockRevision == TRUE)
							      <th scope="col">@lang('Increase revision stock')</th>
						      @endif
						      @if($subtractStock == TRUE)
							      <th scope="col">@lang('Subtract')</th>
						      @endif
						      @if($subtractStockRevision == TRUE)
							      <th scope="col">@lang('Subtract revision stock')</th>
						      @endif
						    </tr>
						  </thead>
						  <tbody>

					        @foreach($childrens->sortBy('size.name') as $children)

							    <tr>
							      <th scope="row">{{ $children->id }}</th>
							      <td>{{ $children->color->name}}</td>
							      <td>{{ $children->size->name}}</td>
							      <td>{{ $children->stock }}</td>
							      <td>{{ $children->stock_revision }}</td>
							      <td>{{ $children->stock_store }}</td>
							      @if($increaseStock == TRUE)
								      <td style="width:100px; max-width: 100px;">
								      	<input class="form-control is-valid" style="background-image: none; padding-right: inherit;" wire:model="inputincrease.{{ $children->id }}.stock" wire:keydown.enter="increase({{ $children->id }})" type="number" min="1" placeholder="+" required>
								      </td>
								  @endif

							      @if($increaseStockRevision == TRUE)
								      <td style="width:100px; max-width: 100px;">
								      	<input class="form-control is-valid" style="background-image: none; padding-right: inherit;" wire:model="inputincreaserevision.{{ $children->id }}.stock" wire:keydown.enter="increase({{ $children->id }})" type="number" min="1" placeholder="+" required>
								      </td>
								  @endif

							      @if($subtractStock == TRUE)
								      <td style="width:100px; max-width: 100px;">
								      	<input class="form-control is-invalid" style="background-image: none; padding-right: inherit;" wire:model="inputsubtract.{{ $children->id }}.stock" wire:keydown.enter="increase({{ $children->id }})" type="number" min="1" placeholder="-" required>
								      </td>
								  @endif

							      @if($subtractStockRevision == TRUE)
								      <td style="width:100px; max-width: 100px;">
								      	<input class="form-control is-invalid" style="background-image: none; padding-right: inherit;" wire:model="inputsubtractrevision.{{ $children->id }}.stock" wire:keydown.enter="increase({{ $children->id }})" type="number" min="1" placeholder="-" required>
								      </td>
								  @endif

							    </tr>

						    @endforeach
						  </tbody>
						</table>
						</div>
				        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>

				        {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
				      </div>
				    </div>
				    @endforeach
			    @endif
			</div>
		</div>
	</x-slot>
    <x-slot name="footer">

        <x-utils.delete-button :href="route('admin.product.destroy', $model->id)" />

		<footer class="blockquote-footer float-right">
			Mies Van der Rohe <cite title="Source Title">Less is more</cite>
		</footer>
	</x-slot>
</x-backend.card> 




@push('after-scripts')

    <script>
	  $.fn.select2.defaults.set( "theme", "bootstrap4" );
      $(document).ready(function() {
        $('#colorselect').select2({
          placeholder: '@lang("Choose color")',
          // width: 'resolve',
          // theme: 'bootstrap4',
		  // containerCssClass: ':all:',
          // allowClear: true,
          ajax: {
                url: '{{ route('admin.color.select') }}',
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    }
                },
                cache: true,
                delay: 250,
                dropdownautowidth: true
            }
          });

          $('#colorselect').on('change', function (e) {
              @this.set('color_id_select', e.target.value);
          });

      });
    </script>



    <script>
      $(document).ready(function() {
        $('#sizeselect').select2({
          placeholder: '@lang("Choose size")',
          // width: 'resolve',
          // theme: 'bootstrap4',
          // allowClear: true,
          ajax: {
                url: '{{ route('admin.size.select') }}',
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    }
                },
                cache: true,
                delay: 250,
                dropdownautowidth: true
            }
          });

          $('#sizeselect').on('change', function (e) {
              @this.set('size_id_select', e.target.value);
          });

      });
    </script>


    <script>
      $(document).ready(function() {
        $('#colorselectmultiple').select2({
          placeholder: '@lang("Choose colors")',
          width: 'resolve',
          // theme: 'bootstrap4',
          allowClear: true,
          multiple: true,
          ajax: {
                url: '{{ route('admin.color.select') }}',
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    }
                },
                cache: true,
                delay: 250,
                dropdownautowidth: true
            }
          });

          $('#colorselectmultiple').on('change', function (e) {
            var data = $('#colorselectmultiple').select2("val");
            @this.set('colorsmultiple_id', data);
          });

      });
    </script>

    <script>
      $(document).ready(function() {
        $('#sizeselectmuliple').select2({
          placeholder: '@lang("Choose sizes")',
          width: 'resolve',
          // theme: 'bootstrap4',
          allowClear: true,
          multiple: true,
          ajax: {
                url: '{{ route('admin.size.select') }}',
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                dataType: 'json',
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        }),
                        pagination: {
                            more: data.pagination
                        }
                    }
                },
                cache: true,
                delay: 250,
                dropdownautowidth: true
            }
          });

          $('#sizeselectmuliple').on('change', function (e) {
            var data = $('#sizeselectmuliple').select2("val");
            @this.set('sizesmultiple_id', data);
          });


      });
    </script>



@endpush
