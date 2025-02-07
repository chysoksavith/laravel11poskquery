@extends('layouts.app')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Sale</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Sale
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
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Sale List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addSaleModal">
                                    Add Sale
                                </button>
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
                            <table class="table table-bordered" id="sale_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Member</th>
                                        <th>User</th>
                                        <th>Total Items</th>
                                        <th>Total Price</th>
                                        <th>Discount</th>
                                        <th>Accepted</th>
                                        <th>Create at</th>
                                        <th>Update at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    <!-- Data will be injected here by AJAX -->
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
    <div class="modal fade" id="addSaleModal" tabindex="-1" role="dialog" aria-labelledby="addSaleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSaleModalLabel">Add Sale</h5>
                </div>
                <div class="modal-body">
                    <form id="saleForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Member</label>
                            <select name="member_id" id="member_id" class="form-select">
                                <option value="">Select Member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name_member }}</option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Item</label>
                            <input type="number" class="form-control" id="total_item" name="total_item" step="0.01"
                                required>
                            @error('total_item')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Price</label>
                            <input type="number" class="form-control" id="total_price" name="total_price" step="0.01"
                                required>
                            @error('total_price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount</label>
                            <input type="number" class="form-control" id="discount" name="discount" step="0.01">
                            @error('discount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Accepted</label>
                            <select name="accepted" id="accepted" class="form-select">
                                <option value="">Selected</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>

                            </select>
                            @error('accepted')
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

            function fetchData(page = 1, search = '') {
                $.ajax({
                    url: "{{ url('admin/sales/data') }}",
                    method: "GET",
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
                            response.data.forEach(sale => {
                                let createdAt = dayjs(sale.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updatedAt = dayjs(sale.updated_at).format(
                                    'MM-DD-YYYY h:mm A');

                                tableBody += `
                        <tr>
                            <td>${sale.id}</td>
                            <td>${sale.member.name_member}</td>
                            <td>${sale.user.name}</td>
                            <td>${sale.total_item}</td>
                            <td>${sale.total_price} $</td>
                            <td>${sale.discount} %</td>
                            <td>${sale.accepted}</td>

                            <td>${createdAt}</td>
                            <td>${updatedAt}</td>
                            <td>
                                <a href="{{ url('admin/sales/sale_detail_list') }}/${sale.id}" class="btn btn-info">Sale Detail</a>
                                <button class="btn btn-warning edit-btn" data-id="${sale.id}">Edit</button>
                                <button class="btn btn-danger delete-btn" data-id="${sale.id}">Delete</button>
                            </td>
                        </tr>`;
                            });
                        }
                        $('#sale_table tbody').html(tableBody);
                        renderPagination(response);

                    }
                })
            }
            fetchData(currentPage, searchQuery);

            $('[data-target="#addSaleModal"]').on('click', function() {
                $('#addSaleModal .modal-title').text('Add Sale');
                $('#saleForm')[0].reset();
                $('.text-danger').remove();
                $('#saleForm').off('submit').on('submit', handleCreate);
            });
            $('#sale_table tbody').on('click', '.edit-btn', handleEdit);
            $('#sale_table tbody').on('click', '.delete-btn', handleDelete);

            // create function
            function handleCreate(e) {
                e.preventDefault();
                $('.text-danger').remove()
                const formData = new FormData($('#saleForm')[0]);

                $.ajax({
                    url: "{{ url('admin/sales/store') }}",
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
                        fetchData(currentPage, searchQuery);
                        $('#saleForm')[0].reset();
                        $('#addSaleModal').modal('hide');
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        for (const key in errors) {
                            $(`#${key}`).after(`<span class="text-danger">${errors[key][0]}</span>`);
                        }
                    }
                })
            }
            // edit function
            function handleEdit() {
                const id = $(this).data('id');
                $('.text-danger').remove();
                $.ajax({
                    url: `{{ url('admin/sales/edit') }}/${id}`,
                    method: "GET",
                    success: function(response) {
                        $('#addSaleModal').modal('show');
                        $('#addSaleModal .modal-title').text('Update Sale');
                        $('#member_id').val(response.member_id);
                        $('#user_id').val(response.user_id);
                        $('#total_item').val(response.total_item);
                        $('#total_price').val(response.total_price);
                        $('#discount').val(response.discount);
                        $('#accepted').val(response.accepted);

                        $('#saleForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            let formData = new FormData(this);
                            $.ajax({
                                url: `{{ url('admin/sales/update') }}/${id}`,
                                method: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    $('.flashMessage')
                                        .text(response.success)
                                        .removeClass('alert-danger')
                                        .addClass('alert-success')
                                        .fadeIn()
                                        .delay(3000)
                                        .fadeOut();
                                    fetchData(currentPage, searchQuery);
                                    $('#saleForm')[0].reset();
                                    $('#addSaleModal').modal('hide');
                                },
                                error: function(xhr) {
                                    const errors = xhr.responseJSON.errors;
                                    for (const key in errors) {
                                        $(`#${key}`).after(
                                            `<span class="text-danger">${errors[key][0]}</span>`
                                        );
                                    }
                                }
                            })
                        })
                    }
                })
            }
            // delete function
            function handleDelete(e) {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete?')) {
                    $.ajax({
                        url: `{{ url('admin/sales/delete') }}/${id}`,
                        method: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            fetchData(currentPage, searchQuery);
                            $('.flashMessage')
                                .text(res.success)
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        }
                    });
                }
            }
            // search
            $('#search-input').on('input', function() {
                searchQuery = $(this).val();
                fetchData(1, searchQuery);
            });

            // Manual search on button click
            $('#search-button').on('click', function() {
                searchQuery = $('#search-input').val();
                fetchData(1, searchQuery);
            });

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
                        fetchData(currentPage,
                            searchQuery); // Fetch products for the current page and search query
                    }
                });
            }
        });
    </script>
@endsection
