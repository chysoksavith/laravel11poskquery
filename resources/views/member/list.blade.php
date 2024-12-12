@extends('layouts.app')
@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Member</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Member
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
                                Member List
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addMemberModal">
                                    Add Member
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered" id="member-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code Member</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Phone Number</th>
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
    <div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModal">Add Member</h5>
                </div>
                <div class="modal-body">
                    <form id="memberForm" method="POST" action="{{ url('admin/member/store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code Member</label>
                            <input type="text" class="form-control" id="code_member" name="code_member">
                            @error('code_member')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="name_member" name="name_member">
                            @error('name_member')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address">
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="number" class="form-control" id="telephone" name="telephone">
                            @error('telephone')
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
            function fetchMember() {
                $.ajax({
                    url: "{{ url('admin/member/data') }}",
                    method: "GET",
                    success: function(response) {
                        let tableBody = '';
                        if (response) {
                            $.each(response, function(index, member) {
                                let createdAt = dayjs(member.created_at).format(
                                    'MM-DD-YYYY h:mm A');
                                let updateAt = dayjs(member.updated_at).format(
                                    'MM-DD-YYYY h:mm A');
                                tableBody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${member.code_member}</td>
                                <td>${member.name_member}</td>
                                <td>${member.address}</td>
                                <td>${member.telephone}</td>
                                <td>${createdAt}</td>
                                <td>${updateAt}</td>
                                <td>
                                    <button class="btn btn-warning edit-btn" data-id="${member.id}">Edit</button>
                                    <button class="btn btn-danger delete-btn" data-id="${member.id}">Delete</button>
                                </td>
                            </tr>
                        `;
                            });
                            $('#member-table tbody').html(tableBody);
                            $('.edit-btn').on('click', handleEdit);
                            $('.delete-btn').on('click', handleDelete);

                        } else {
                            console.error("No members found or invalid response structure.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error: ", error);
                    }
                });
            }

            fetchMember();

            // Clear form and text-danger messages when opening modal
            $('[data-target="#addMemberModal"]').on('click', function() {
                $('#addMemberModal .modal-title').text('Add Member'); // Set title to Add
                $('#memberForm')[0].reset(); // Clear the form
                $('.text-danger').remove(); // Remove error messages
                $('#memberForm').off('submit').on('submit', handleCreate); // Reset form submission
            });

            function handleCreate(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $('.text-danger').remove(); // Clear error messages
                $.ajax({
                    url: "{{ url('admin/member/store') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        $('#addMemberModal').modal('hide');
                        $('#memberForm')[0].reset();
                        fetchMember();
                        $('.flashMessage')
                            .text(response.success)
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        for (const key in errors) {
                            $(`#${key}`).after(`<span class="text-danger">${errors[key][0]}</span>`);
                        }
                    }
                });
            }

            function handleEdit(e) {
                e.preventDefault();
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url('admin/member/edit') }}/${id}`,
                    method: "GET",
                    success: function(response) {
                        $('#addMemberModal').modal('show');
                        $('#addMemberModal .modal-title').text('Update Member');
                        $('#code_member').val(response.code_member);
                        $('#name_member').val(response.name_member);
                        $('#address').val(response.address);
                        $('#telephone').val(response.telephone);

                        $('.text-danger').remove(); // Clear error messages
                        $('#memberForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            $('.text-danger').remove(); // Ensure no old errors remain
                            const formData = $(this).serialize();
                            $.ajax({
                                url: `{{ url('admin/member/update') }}/${id}`,
                                method: "POST",
                                data: formData,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content'),
                                },
                                success: function(response) {
                                    $('.flashMessage')
                                        .text(response.success)
                                        .removeClass('alert-danger')
                                        .addClass('alert-success')
                                        .fadeIn()
                                        .delay(3000)
                                        .fadeOut();
                                    fetchMember();
                                    $('#memberForm')[0].reset();
                                    $('#addMemberModal').modal('hide');
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
                if (confirm('Are you sure you want to delete')) {
                    $.ajax({
                        url: `{{ url('admin/member/delete') }}/${id}`,
                        method: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            fetchMember();
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
