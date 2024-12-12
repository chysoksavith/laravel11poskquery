@extends('layouts.app')
@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Product</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Product
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
                    <div class="cart mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                Product List
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addProductModal">
                                    Add Product
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered" id="product-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Product Price</th>
                                        <th>Selling Price</th>
                                        <th>Stock</th>
                                        <th>Create at</th>
                                        <th>Update at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flashMessage alert alert-success" style="display: none"></div>
    <!-- Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModal">Add Category</h5>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST" action="{{ url('admin/product/store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name">
                            @error('product_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Code</label>
                            <input type="text" class="form-control" id="product_code" name="product_code">
                            @error('product_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">Select Category</option>
                                @foreach ($categories as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <select name="brand_id" id="brand_id" class="form-select">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Selling Price</label>
                            <input type="number" class="form-control" id="selling_price" name="selling_price">
                            @error('selling_price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Price</label>
                            <input type="number" class="form-control" id="product_price" name="product_price">
                            @error('product_price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Discount</label>
                            <input type="number" class="form-control" id="discount" name="discount">
                            @error('discount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock">
                            @error('stock')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Fetch products and populate the table
            function fetchProduct() {
                $.ajax({
                    url: "{{ url('admin/product/data') }}",
                    type: "GET",
                    success: function(response) {
                        if (response) {
                            let tableBody = '';
                            $.each(response, function(index, product) {
                                let createdAt = dayjs(product.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updatedAt = dayjs(product.updated_at).format(
                                    'MM-DD-YYYY h:mm A');
                                tableBody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${product.product_name}</td>
                                <td>${product.category.category_name}</td>
                                <td>${product.brand.name}</td>
                                <td>${product.product_price}$</td>
                                <td>${product.selling_price}$</td>
                                <td>${product.stock}</td>
                                <td>${createdAt}</td>
                                <td>${updatedAt}</td>
                                <td>
                                    <button class="btn btn-warning edit-btn" data-id="${product.id}">Edit</button>
                                    <button class="btn btn-danger delete-btn" data-id="${product.id}">Delete</button>
                                </td>
                            </tr>`;
                            });
                            $('#product-table tbody').html(tableBody);

                            // Attach Edit and Delete Handlers
                            $('.edit-btn').on('click', handleEdit);
                            $('.delete-btn').on('click', handleDelete);
                        }
                    }
                });
            }
            fetchProduct();

            // Open Add Product Modal
            $('[data-target="#addProductModal"]').on('click', function() {
                $('#addProductModal .modal-title').text('Add Product'); // Set title
                $('#productForm')[0].reset(); // Clear the form
                $('.text-danger').remove(); // Remove error messages
                $('#productForm').off('submit').on('submit', handleCreate); // Set Create handler
            });

            // Handle Create Product
            function handleCreate(e) {
                e.preventDefault();
                $('.text-danger').remove();
                const formData = $('#productForm').serialize();
                $.ajax({
                    url: "{{ url('admin/product/store') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('.flashMessage')
                            .text(response.success)
                            .removeClass('alert-danger')
                            .addClass('alert-success')
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                        fetchProduct();
                        $('#productForm')[0].reset();
                        $('#addProductModal').modal('hide');
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        for (const key in errors) {
                            $(`#${key}`).after(`<span class="text-danger">${errors[key][0]}</span>`);
                        }
                    }
                });
            }

            // Handle Edit Product
            function handleEdit() {
                const id = $(this).data('id');
                $('.text-danger').remove(); // Remove error messages

                $.ajax({
                    url: `{{ url('admin/product/edit') }}/${id}`,
                    method: "GET",
                    success: function(response) {
                        // Populate Modal with Existing Data
                        $('#addProductModal').modal('show');
                        $('#addProductModal .modal-title').text('Update Product');
                        $('#category_id').val(response.category_id);
                        $('#brand_id').val(response.brand_id);
                        $('#product_name').val(response.product_name);
                        $('#product_code').val(response.product_code);
                        $('#product_price').val(response.product_price);
                        $('#selling_price').val(response.selling_price);
                        $('#discount').val(response.discount);
                        $('#stock').val(response.stock);

                        // Set Form Submission to Update
                        $('#productForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            $('.text-danger').remove();
                            const formData = $(this).serialize();
                            $.ajax({
                                url: `{{ url('admin/product/update') }}/${id}`,
                                method: "POST",
                                data: formData,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function(response) {
                                    $('.flashMessage')
                                        .text(response.success)
                                        .removeClass('alert-danger')
                                        .addClass('alert-success')
                                        .fadeIn()
                                        .delay(3000)
                                        .fadeOut();
                                    fetchProduct();
                                    $('#productForm')[0].reset();
                                    $('#addProductModal').modal('hide');
                                },
                                error: function(xhr) {
                                    const errors = xhr.responseJSON.errors;
                                    for (const key in errors) {
                                        $(`#${key}`).after(
                                            `<span class="text-danger">${errors[key][0]}</span>`
                                        );
                                    }
                                }
                            });
                        });
                    }
                });
            }
            // delete
            function handleDelete(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete?')) {
                    $.ajax({
                        url: `{{ url('admin/product/delete') }}/${id}`,
                        method: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            fetchProduct(); // Refresh table
                            $('.flashMessage')
                                .text(res.success)
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        }
                    })
                }
            }
        });
    </script>
@endsection
