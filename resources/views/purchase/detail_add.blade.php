@extends('layouts.app')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Purchase Detail Add</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Purchase Detail Add
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4 p-2">
                        <div class="card-header">
                            <h3 class="card-title">Add Purchase Detail List</h3>
                            <div class="card-tools">
                                <a href="{{ url('admin/purchase') }}" class="btn btn-primary">Back</a>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        <form method="post" action="">
                                            @csrf
                                            <input type="hidden" name="purchase_id" value="{{ $purchase_id }}">
                                            <!-- Select product -->
                                            <div class="mb-3">
                                                <label for="product_id" class="form-label">Product Name</label>
                                                <select class="form-select @error('product_id') is-invalid @enderror"
                                                    name="product_id">
                                                    <option>Select Product</option>
                                                    @foreach ($getProducts as $getProduct)
                                                        <option value="{{ $getProduct->id }}"
                                                            {{ old('product_id') == $getProduct->id ? 'selected' : '' }}>
                                                            {{ $getProduct->product_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('product_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Purchase Price -->
                                            <div class="mb-3">
                                                <label for="purchase_price" class="form-label">Purchase Price</label>
                                                <input type="number"
                                                    class="form-control @error('purchase_price') is-invalid @enderror"
                                                    name="purchase_price" placeholder="Enter your purchase price"
                                                    value="{{ old('purchase_price') }}">
                                                @error('purchase_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Amount -->
                                            <div class="mb-3">
                                                <label for="amount" class="form-label">Amount</label>
                                                <input type="number"
                                                    class="form-control @error('amount') is-invalid @enderror"
                                                    name="amount" placeholder="Enter your Amount"
                                                    value="{{ old('amount') }}">
                                                @error('amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="mb-3">
                                                <label for="subtotal" class="form-label">Sub Total</label>
                                                <input type="number"
                                                    class="form-control @error('subtotal') is-invalid @enderror"
                                                    name="subtotal" placeholder="Enter your subtotal"
                                                    value="{{ old('subtotal') }}">
                                                @error('subtotal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="d-grid gap-1">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="{{ url('admin/purchase') }}" type="submit"
                                                    class="btn btn-danger">Cancel</a>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flashMessage alert alert-success" style="display: none"></div>
@endsection
