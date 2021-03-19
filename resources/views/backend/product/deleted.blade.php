@extends('backend.layouts.app')

@section('title', __('Product'))

@section('breadcrumb-links')
    @include('backend.product.includes.breadcrumb-links')
@endsection


@section('content')

    <x-backend.card>
        <x-slot name="header">
            <strong style="color: red;"> @lang('Deleted products') </strong>
        </x-slot>


        <x-slot name="headerActions">
            <x-utils.link
	                style="color: green;"
                    icon="c-icon cil-plus"
                    class="card-header-action"
                    :href="route('admin.product.create')"
                    :text="__('Create product')"
            />
        </x-slot>

        <x-slot name="body">
    		<livewire:backend.product.product-table status="deleted"/>
		</x-slot>
	</x-backend.card>

@endsection
