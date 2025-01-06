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
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Expense List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addExpenseModal">
                                    Add Expense
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered" id="expense_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Amount</th>
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
    <div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
                </div>
                <div class="modal-body">
                    <form id="expenseForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" id="description" cols="10" rows="5"></textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                required>
                            @error('amount')
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

            function fetchData(page = 1) {
                $.ajax({
                    url: "{{ url('admin/expense/data') }}",
                    type: "GET",
                    data: {
                        page
                    },
                    success: function(response) {
                        let tableBody = '';
                        let totalAmount = 0;

                        if (response.data.length === 0) {
                            tableBody = `
                                <tr>
                                    <td colspan="6" class="text-center">No Data Available</td>
                                </tr>`;
                        } else {
                            $.each(response.data, function(index, expense) {
                                let createdAt = dayjs(expense.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updatedAt = dayjs(expense.updated_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let amount = expense.amount !== null ? `${expense.amount} $` :
                                    'null';

                                tableBody += `
                                    <tr>
                                        <td>${expense.id}</td>
                                        <td>${expense.description}</td>
                                        <td>${expense.amount} $</td>
                                        <td>${createdAt}</td>
                                        <td>${updatedAt}</td>
                                        <td>
                                            <button class="btn btn-warning edit-btn" data-id="${expense.id}">Edit</button>
                                            <button class="btn btn-danger delete-btn" data-id="${expense.id}">Delete</button>
                                        </td>
                                    </tr>`;
                                if (expense.amount !== null) {
                                    totalAmount += parseFloat(expense
                                        .amount); // Add the amount to the total
                                }
                            });
                            tableBody += `
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                        <td colspan="4"><strong>${totalAmount.toFixed(2)} $</strong></td>
                                    </tr>`;
                        }

                        $('#tbody').html(tableBody);
                        $('.edit-btn').on('click', handleEdit);
                        $('.delete-btn').on('click', handleDelete);
                        renderPagination(response);

                    }
                });
            }
            // Open Add Product Modal
            $('[data-target="#addExpenseModal"]').on('click', function() {
                $('#addExpenseModal .modal-title').text('Add Expense'); // Set title
                $('#expenseForm')[0].reset(); // Clear the form
                $('.text-danger').remove(); // Remove error messages
                $('#expenseForm').off('submit').on('submit', handleCreate); // Set Create handler
                // disable
            });

            function handleCreate(e) {
                e.preventDefault();
                $('.text-danger').remove();
                const formData = new FormData($('#expenseForm')[0]);
                $.ajax({
                    url: "{{ url('admin/expense/store') }}",
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
                        fetchData(currentPage);
                        $('#expenseForm')[0].reset();
                        $('#addExpenseModal').modal('hide');
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
            }

            function handleEdit() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url('admin/expense/edit') }}/${id}`,
                    method: "GET",
                    success: function(response) {
                        $('#addExpenseModal').modal('show');
                        $('#description').val(response.description);
                        $('#amount').val(response.amount);
                        $('#expenseForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            $('.text-danger').remove();
                            let formData = new FormData(
                                this);

                            $.ajax({
                                url: `{{ url('admin/expense/update') }}/${id}`,
                                method: "POST",
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
                                    fetchData(currentPage);
                                    $('#expenseForm')[0].reset();
                                    $('#addExpenseModal').modal('hide');
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

            function handleDelete(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (confirm('are you sure you want to delete?')) {
                    $.ajax({
                        url: `{{ url('admin/expense/delete') }}/${id}`,
                        method: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },

                        success: function(response) {
                            fetchData(currentPage);
                            $('.flashMessage')
                                .text(response.success)
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
                    })
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
                        fetchData(currentPage);
                    }
                });
            }
            fetchData(currentPage);
        });
    </script>
@endsection
