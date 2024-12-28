@extends('layouts.app')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Products in {{ $category->category_name }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pos.view') }}">POS</a></li>
                        <li class="breadcrumb-item active">{{ $category->category_name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Include the Category Header Component -->
                    @include('components.categoryHeader', ['categories' => $categories])
                </div>

                <div class="col-md-9">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Products</h4>
                        </div>
                        <div class="card-body">
                            @if ($category->products->count())
                                <ul>
                                    @foreach ($category->products as $product)
                                        <li>
                                            <strong>{{ $product->product_name }}</strong> - ${{ $product->selling_price }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No products found in this category.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
