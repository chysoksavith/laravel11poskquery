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
                        <div class="card-body p-2">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <input type="text" id="search-input" class="form-control"
                                        placeholder="Search Product">
                                </div>
                                <div class="col-md-2">
                                    <button id="search-button" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                            <table class="table table-bordered" id="product-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Image</th>
                                        <th>Product Code</th>
                                        <th>Selling Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Create at</th>
                                        <th>Update at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">

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
    <!-- Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModal">Add Category</h5>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST" action="{{ url('admin/product/store') }}"
                        enctype="multipart/form-data">
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
                            <input type="text" class="form-control"" id="product_code" name="product_code" readonly>
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

                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" id="image" required>
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <img id="previewImage" src="#" alt="Image Preview"
                                style="max-width: 100px; display: none;">
                            <button type="button" id="removeImageBtn" class="btn btn-danger"
                                style="display: none;">Remove Image</button>

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
            let currentPage = 1;
            let searchQuery = '';

            // Fetch products and populate the table
            function fetchProduct(page = 1, search = '') {
                $.ajax({
                    url: "{{ url('admin/product/data') }}",
                    type: "GET",
                    data: {
                        page,
                        search
                    },
                    success: function(response) {
                        let tableBody = '';
                        if (response.data.length === 0) {
                            tableBody = `
                    <tr>
                        <td colspan="10" class="text-center">No Data Available</td>
                    </tr>`;
                        } else {
                            $.each(response.data, function(index, product) {
                                let createdAt = dayjs(product.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updatedAt = dayjs(product.updated_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let statusIcon = product.status == 1 ?
                                    "<i class='fas fa-toggle-on' style='color:blue;' status='Active'></i>" :
                                    "<i class='fas fa-toggle-off' style='color:grey;' status='Inactive'></i>";
                                let imageUrl = product.image ? '/storage/' + product.image :
                                    'null';
                                let imageDisplay = imageUrl === 'null' ? 'No image ' :
                                    `<img src="${imageUrl}" alt="Product Image" style="max-width: 100px;" />`;
                                tableBody += `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.product_name}</td>
                        <td>${product.category.category_name}</td>
                        <td>${imageDisplay}</td>
                        <td>${product.product_code}</td>
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
                        }
                        $('#product-table tbody').html(tableBody);
                        renderPagination(response);
                        // Attach Edit and Delete Handlers
                        $('.edit-btn').on('click', handleEdit);
                        $('.delete-btn').on('click', handleDelete);
                    }
                });
            }



            // Open Add Product Modal
            $('[data-target="#addProductModal"]').on('click', function() {
                $('#addProductModal .modal-title').text('Add Product'); // Set title
                $('#productForm')[0].reset(); // Clear the form
                $('.text-danger').remove(); // Remove error messages
                $('#previewImage').hide(); // Hide preview image
                $('#removeImageBtn').hide();
                $.ajax({
                    url: "{{ url('admin/generate-product-code') }}", // Ensure the URL is correct
                    method: "GET",
                    success: function(response) {
                        $('#product_code').val(response
                            .product_code); // Set generated product code
                    },
                    error: function(xhr) {
                        console.error('Failed to generate product code');
                    }
                });
                $('#productForm').off('submit').on('submit', handleCreate); // Set Create handler
                // disable
            });



            // Handle Create Product
            function handleCreate(e) {
                e.preventDefault();
                $('.text-danger').remove();
                const formData = new FormData($('#productForm')[0]);

                $.ajax({
                    url: "{{ url('admin/product/store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
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

                        fetchProduct(currentPage, searchQuery); // Re-fetch the products
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
                        $('#previewImage').attr('src', '/storage/' + response.image).show();
                        $('#removeImageBtn').hide();
                        // Set Form Submission to Update

                        $('#productForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            $('.text-danger').remove();

                            // Create FormData object for both regular fields and file input
                            let formData = new FormData(
                                this); // This includes the image input as well

                            $.ajax({
                                url: `{{ url('admin/product/update') }}/${id}`,
                                method: "POST",
                                data: formData,
                                processData: false, // Don't process the data
                                contentType: false, // Don't set content-type, it's automatically handled by FormData
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
                                    fetchProduct(currentPage,
                                        searchQuery); // Re-fetch the products
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

            // Preview Image
            $('#image').on('change', function() {
                const [file] = this.files;
                if (file) {
                    $('#previewImage').attr('src', URL.createObjectURL(file)).show();
                    $('#removeImageBtn').show();
                }
            });
            // remove preview image
            $('#removeImageBtn').on('click', function() {
                $('#image').val(''); // Reset file input
                $('#previewImage').hide(); // Hide image preview
                $(this).hide(); // Hide remove button
            });
            // Handle Delete
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
                            fetchProduct(currentPage, searchQuery); // Re-fetch the products
                            $('.flashMessage')
                                .text(res.success)
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        },
                        error: function(xhr) {
                            $('.flashMessage')
                                .text(xhr.responseJSON.message)
                                .removeClass('alert-success')
                                .addClass('alert-danger')
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        }
                    });
                }
            }
            // Render pagination
            function renderPagination(data) {
                let pagination = `<nav><ul class="pagination">`;

                if (data.prev_page_url) {
                    pagination += `<li class="page-item">
            <a href="#" class="page-link" data-page="${data.current_page - 1}">Previous</a>
        </li>`;
                }

                for (let i = 1; i <= data.last_page; i++) {
                    pagination += `<li class="page-item ${data.current_page === i ? 'active' : ''}">
            <a href="#" class="page-link" data-page="${i}">${i}</a>
        </li>`;
                }

                if (data.next_page_url) {
                    pagination += `<li class="page-item">
            <a href="#" class="page-link" data-page="${data.current_page + 1}">Next</a>
        </li>`;
                }

                pagination += `</ul></nav>`;
                $('#pagination-links').html(pagination);

                // Bind Click Event to Pagination Links
                $('.page-link').on('click', function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    if (page) {
                        currentPage = page; // Set the current page to the clicked page
                        fetchProduct(currentPage,
                            searchQuery); // Fetch products for the current page and search query
                    }
                });
            }

            // Auto search on input
            $('#search-input').on('input', function() {
                searchQuery = $(this).val();
                fetchProduct(1, searchQuery); // Always start from page 1 on input
            });

            // Manual search on button click
            $('#search-button').on('click', function() {
                searchQuery = $('#search-input').val();
                fetchProduct(1, searchQuery); // Always start from page 1 on search click
            });

            fetchProduct(currentPage, searchQuery);
        });
    </script>
@endsection
