@extends('layouts.app')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Purchase Detail</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Purchase Detail
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    @include('_message')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Purchase Detail List</h3>
                            <div class="card-tools">
                                <a href="{{ url('admin/purchase/detail_add/' . $purchase_id) }}" class="btn btn-primary">Add
                                    Purchase
                                    Detail</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <input type="text" id="search-input" class="form-control"
                                        placeholder="Search Product">
                                </div>
                                <div class="col-md-2">
                                    <button id="search-button" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                            <table class="table table-bordered" id="purchase_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Purchase Id</th>
                                        <th>Product Name</th>
                                        <th>Purchase Price</th>
                                        <th>Amount</th>
                                        <th>Sub Total</th>
                                        <th>Create at</th>
                                        <th>Update at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    @forelse ($getRecord as $value)
                                        <tr>
                                            <td>{{ $value->id }}</td>
                                            <td>{{ $value->purchase_id }}</td>
                                            <td>{{ $value->product->product_name }}</td>
                                            <td>{{ $value->purchase_price }}</td>
                                            <td>{{ $value->amount }}</td>
                                            <td>{{ $value->subtotal }}</td>
                                            <td>{{ $value->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>{{ $value->updated_at->format('d-m-Y H:i:s') }}</td>
                                            <td>
                                                <a href="{{ url('admin/purchase_detail/edit/' . $value->id) }}"
                                                    class="btn btn-primary">Edit</a>
                                                <a href="{{ url('admin/purchase_detail/delete/' . $value->id) }}"
                                                    onclick="return confirm('Are you sure you want to delete?')"
                                                    class="btn btn-danger">Delete</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>no product</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div id="pagination-links" class="mt-3"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flashMessage alert alert-success" style="display: none"></div>
@endsection
