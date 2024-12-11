@extends('layouts.app')
@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Category</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Category
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
                                Category List
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addCategoryModal">
                                    Add Category
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered" id="category-table">
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
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModal">Add Category</h5>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
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

            // Function to fetch categories and update the table
            function fetchCategories() {
                $.ajax({
                    url: "{{ url('admin/category/data') }}",
                    type: "GET",
                    success: function(response) {
                        if (response && response.categories) {
                            let tableBody = '';
                            $.each(response.categories, function(index, category) {
                                let createdAt = dayjs(category.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updateAt = dayjs(category.updated_at).format(
                                    'MM-DD-YYYY h:mm A');
                                tableBody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${category.category_name}</td>
                                <td>${createdAt}</td>
                                <td>${updateAt}</td>
                                <td>
                                    <button class="btn btn-warning edit-btn" data-id="${category.id}">Edit</button>
                                    <button class="btn btn-danger delete-btn" data-id="${category.id}">Delete</button>
                                </td>
                            </tr>
                            `;
                            });
                            $('#category-table tbody').html(tableBody);
                            $('.edit-btn').on('click', handleEdit);
                            $('.delete-btn').on('click', handleDelete);
                        } else {
                            console.error("No categories found or invalid response structure.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error: ", error);
                    }
                });
            }

            // Call fetchCategories on page load
            fetchCategories();

            // Handle the "Add Category" button click
            $('[data-target="#addCategoryModal"]').on('click', function() {
                $('#addCategoryModal .modal-title').text('Add Category'); // Set title to Add
                $('#categoryForm')[0].reset(); // Clear the form
                $('#categoryForm').off('submit').on('submit', handleCreate); // Reset form submission
            });
            // Hnadle the delete function
            function handleDelete() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete?')) {
                    $.ajax({
                        url: `{{ url('admin/category/delete') }}/${id}`,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            fetchCategories();
                            $('.flashMessage')
                                .text(response.success)
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        },
                        error: function(xhr, status, error) {
                            console.log("AJAX Error: ", error);
                        }
                    })
                }
            }
            // Handle the "Edit" button click
            function handleEdit() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url('admin/category/edit') }}/${id}`,
                    type: "GET",
                    success: function(category) {
                        $('#addCategoryModal .modal-title').text(
                            'Update Category'); // Set title to Update
                        $('#category_name').val(category.category_name); // Populate form field
                        $('#addCategoryModal').modal('show'); // Show modal

                        // Update form submission for editing
                        $('#categoryForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            const formData = $(this).serialize();
                            $.ajax({
                                url: `{{ url('admin/category/update') }}/${id}`,
                                type: "POST",
                                data: formData,
                                success: function(response) {
                                    $('#addCategoryModal').modal(
                                        'hide'); // Hide modal
                                    fetchCategories(); // Refresh table
                                    $('#categoryForm')[0].reset(); // Reset form
                                    $('.flashMessage')
                                        .text(response.success)
                                        .fadeIn()
                                        .delay(3000)
                                        .fadeOut();
                                },
                                error: function(xhr, status, error) {
                                    console.log("AJAX Error: ", error);
                                }
                            });
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error during edit fetch: ", error);
                    }
                });
            }

            // Handle creating a new category
            function handleCreate(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: "{{ url('admin/category/store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $('#addCategoryModal').modal('hide'); // Hide modal
                        $('#categoryForm')[0].reset(); // Reset form
                        $('.flashMessage')
                            .text(response.success)
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                        fetchCategories(); // Refresh table
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error: ", error);
                    }
                });
            }
        });
    </script>
@endsection
