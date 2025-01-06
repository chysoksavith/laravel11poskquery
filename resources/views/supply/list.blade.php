@extends('layouts.app')
@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Supply</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Supply
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
                                Supply List
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addSupplyModal">
                                    Add Supply
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-4 mt-4">
                                <div class="col-md-4 ">
                                    <input type="text" id="search-input" class="form-control"
                                        placeholder="Search Product">
                                </div>
                                <div class="col-md-2">
                                    <button id="search-button" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                            <table class="table table-bordered" id="Supply_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                        <th>Address</th>
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
    <div class="modal fade" id="addSupplyModal" tabindex="-1" role="dialog" aria-labelledby="addSupplyModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplyModal">Add Supply</h5>
                </div>
                <div class="modal-body">
                    <form id="SupplyForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                            @error('supplier_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="supplier_telephone" name="supplier_telephone"
                                required>
                            @error('supplier_telephone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" id="supplier_address" name="supplier_address"
                                required>
                            @error('supplier_address')
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
            let currentPage = 1;
            let searchQuery = '';

            function fetchSupplier(page = 1, search = '') {
                $.ajax({
                    url: "{{ url('admin/supplier/data') }}",
                    type: "GET",
                    data: {
                        page: page,
                        search: search
                    },
                    success: function(response) {
                        // console.log('Data:', response);
                        // console.log('Current Page:', response.current_page);
                        // console.log('Total Pages:', response.last_page);
                        // console.log('Total Items:', response.total);
                        // console.log('Items per Page:', response.per_page);
                        let tableBody = '';
                        if (response.data.length === 0) {
                            tableBody = `
                        <tr>
                            <td colspan="7" class="text-center">No Data Available</td>
                        </tr>`;
                        } else {
                            $.each(response.data, function(index, supply) {
                                let createdAt = dayjs(supply.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updatedAt = dayjs(supply.updated_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let statusIcon = supply.status == 1 ?
                                    "<i class='fas fa-toggle-on' style='color:blue;' status='Active'></i>" :
                                    "<i class='fas fa-toggle-off' style='color:grey;' status='Inactive'></i>";

                                tableBody += `
                            <tr>
                                <td>${supply.id}</td>
                                <td>${supply.supplier_name}</td>
                                <td>${supply.supplier_telephone}</td>
                                <td>${supply.supplier_address}</td>
                                <td>${createdAt}</td>
                                <td>${updatedAt}</td>
                                <td>
                                    <button class="btn btn-warning edit-btn" data-id="${supply.id}">Edit</button>
                                    <button class="btn btn-danger delete-btn" data-id="${supply.id}">Delete</button>
                                </td>
                            </tr>`;
                            });
                        }
                        $('#Supply_table tbody').html(tableBody);
                        $('.edit-btn').on('click', handleEdit);
                        $('.delete-btn').on('click', handleDelete);
                        renderPagination(response);
                    }
                });
            }

            // Open modal and prepare form for creating new supplier
            $('[data-target="#addSupplyModal"]').on('click', function() {
                $('#addSupplyModal .modal-title').text('Add Supplier');
                $("#SupplyForm")[0].reset();
                $('.text-danger').remove();
                $('#SupplyForm').off('submit').on('submit', handleCreate);
            });

            // Handle the creation of a new supplier
            function handleCreate(e) {
                e.preventDefault();
                $('.text-danger').remove();
                const formData = new FormData($('#SupplyForm')[0]);

                $.ajax({
                    url: "{{ url('admin/supplier/store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Show success message
                        $('.flashMessage')
                            .text(response.success)
                            .removeClass('alert-danger')
                            .addClass('alert-success')
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();

                        // Reload supplier list
                        fetchSupplier(currentPage, searchQuery);
                        $("#SupplyForm")[0].reset();
                        $("#addSupplyModal").modal('hide');
                    },
                    error: function(xhr, status, error) {
                        // Handle errors (validation or others)
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).after('<span class="text-danger">' + value[0] +
                                '</span>');
                        });
                    }
                });
            }

            function handleEdit() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url('admin/supplier/edit') }}/${id}`,
                    type: "GET",
                    success: function(response) {

                        $('#addSupplyModal').modal('show');
                        $('#addSupplyModal .modal-title').text('Update Product');
                        $('#supplier_name').val(response.supplier_name)
                        $('#supplier_telephone').val(response.supplier_telephone)
                        $('#supplier_address').val(response.supplier_address)
                        $('#SupplyForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            $('.text-danger').remove();
                            let formData = new FormData(this);
                            $.ajax({
                                url: `{{ url('admin/supplier/update') }}/${id}`,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
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
                                    fetchSupplier(currentPage, searchQuery);
                                    $("#SupplyForm")[0].reset();
                                    $("#addSupplyModal").modal('hide');
                                },
                                error: function(xhr, status, error) {
                                    var errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, value) {
                                        $('#' + key).after(
                                            '<span class="text-danger">' +
                                            value[0] +
                                            '</span>');
                                    });
                                }
                            })
                        })
                    }
                })
            }

            function handleDelete(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete?')) {
                    $.ajax({
                        url: `{{ url('admin/supplier/delete') }}/${id}`,
                        type: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('.flashMessage')
                                .text(response.success)
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                            fetchSupplier(currentPage, searchQuery);

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

                    })
                }
            }
            // paginate
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
                        fetchSupplier(currentPage, searchQuery);

                    }
                });
            }
            // search
            $('#search-input').on('input', function() {
                searchQuery = $(this).val();
                fetchSupplier(1, searchQuery);
            })
            $('#search-button').on('click', function() {
                searchQuery = $('#search-input').val();
                fetchProduct(1, searchQuery);
            });
            fetchSupplier(currentPage, searchQuery);
        });
    </script>
@endsection
