@extends('layouts.app')
@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Brand</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Brand
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
                                Brand List
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addBrandModal">
                                    Add Brand
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered" id="brand_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
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
    <div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModal">Add Brand</h5>
                </div>
                <div class="modal-body">
                    <form id="brandForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
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
            function fetchBrands() {
                $.ajax({
                    url: "{{ url('admin/brand/data') }}",
                    type: "GET",
                    success: function(response) {
                        if (response) {
                            let tableBody = '';
                            $.each(response, function(index, brand) {
                                let createdAt = dayjs(brand.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updateAt = dayjs(brand.updated_at).format(
                                    'MM-DD-YYYY h:mm A');
                                tableBody += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${brand.name}</td>
                            <td>${createdAt}</td>
                            <td>${updateAt}</td>
                            <td>
                                <button class="btn btn-warning edit-btn" data-id="${brand.id}">Edit</button>
                                <button class="btn btn-danger delete-btn" data-id="${brand.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                            });

                            // Now update the table after the loop
                            $('#brand_table tbody').html(tableBody);

                            // Re-attach event handlers to new delete buttons after table update
                            $('.edit-btn').on('click', handleEdit);
                            $('.delete-btn').on('click', handleDelete);
                        } else {
                            console.error("No Brand found or invalid response structure.");
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            fetchBrands();
            // Handle the "Add Category" button click
            $('[data-target="#addBrandModal"]').on('click', function() {
                $('#addBrandModal .modal-title').text('Add Brand'); // Set title to Add
                $('#brandForm')[0].reset(); // Clear the form
                $('#brandForm').off('submit').on('submit', handleCreate); // Reset form submission
            });

            function handleCreate(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: "{{ url('admin/brand/store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $('#addBrandModal').modal('hide');
                        $('#brandForm')[0].reset();
                        $('.flashMessage')
                            .text(response.success)
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                        fetchBrands();
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error: ", error);
                    }
                })
            }
            // edit brand
            function handleEdit() {
                const id = $(this).data('id');
                console.log(id)
                $.ajax({
                    url: `{{ url('admin/brand/edit') }}/${id}`,
                    type: "GET",
                    success: function(brand) {
                        if (brand) {
                            $('#addBrandModal .modal-title').text('Update Brand');
                            $('#name').val(brand.name);
                            $('#addBrandModal').modal('show');

                            $('#brandForm').off('submit').on('submit', function(e) {
                                e.preventDefault();
                                const formData = $(this).serialize();
                                $.ajax({
                                    url: `{{ url('admin/brand/update') }}/${id}`,
                                    type: "POST",
                                    data: formData,
                                    success: function(response) {
                                        $('#addBrandModal').modal('hide');
                                        $('#brandForm')[0].reset(); // Reset form
                                        fetchBrands();
                                        $('.flashMessage')
                                            .text(response.success)
                                            .fadeIn()
                                            .delay(3000)
                                            .fadeOut();

                                    }
                                })
                            })
                        } else {
                            alert("No Data");
                        }
                    }
                })
            }
            // delete
            function handleDelete(e) {
                e.preventDefault();
                const id = $(this).data('id'); // Get the brand ID
                if (confirm("Are you sure you want to delete this brand?")) {
                    $.ajax({
                        url: `{{ url('admin/brand/delete') }}/${id}`, // Dynamic URL for deletion
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}" // CSRF token for Laravel
                        },
                        success: function(response) {
                            if (response.success) {
                                fetchBrands(); // Refresh the brand list
                                $('.flashMessage')
                                    .text(response.success)
                                    .removeClass("alert-danger")
                                    .addClass("alert-success")
                                    .fadeIn()
                                    .delay(3000)
                                    .fadeOut();
                            } else {
                                console.error("Unexpected response:", response);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", error);
                            $('.flashMessage')
                                .text("Failed to delete the brand. Please try again.")
                                .addClass("alert-danger")
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        }
                    });
                }
            }

        });
    </script>
@endsection
