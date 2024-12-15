@extends('pages.dashboard.layouts.main')
@section('head-script')
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('main')
    <div class="d-flex justify-content-between flex-md-nowrap align-items-center border-bottom mb-3 flex-wrap pt-3 pb-2">
        <h3>Users</h3>
    </div>
    <table class="table-striped table-bordered w-100 table" id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                @php
                    $delete_tlp = auth()->user()->id == $user->id ? 'Untuk alasan keamanan, kamu tidak diperbolehkan menghapus akunmu sendiri' : 'Delete this user';
                    $edit_tlp = auth()->user()->id == $user->id ? 'Untuk alasan keamanan, kamu tidak diperbolehkan mengubah rolemu sendiri' : "Edit this user's role";
                @endphp
                <tr>
                    <td>{{ $user->id }}</td>
                    <td id="{{ $user->id }}"><span class="badge text-bg-{{ $user->is_admin ? 'danger' : 'success' }}">{{ $user->is_admin ? 'Admin' : 'Student' }}</span></td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-right">
                        <div class="d-grid d-flex gap-2">
                            <a class="btn btn-sm btn-warning" id="detail" data-bs-toggle="tooltip" data-bs-title="View this user's detail" href="{{ route('users.show', $user->id) }}"><i class="ti ti-eye"></i></a>
                            <span data-bs-toggle="tooltip" data-bs-title="{{ $edit_tlp }}" tabindex="0">
                                <button class="role-edit btn btn-sm btn-primary {{ auth()->user()->id == $user->id ? 'disabled' : '' }}" id="edit" data-id="{{ $user->id }}" data-role="{{ $user->is_admin }}" data-href="{{ route('users.update', $user->id, false) }}" data-user="{{ $user->full_name }}"><i class="ti ti-edit"></i></button>
                            </span>
                            <form action="{{ route('users.destroy', $user->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <span data-bs-toggle="tooltip" data-bs-title="{{ $delete_tlp }}" tabindex="0">
                                    <button class="btn btn-sm btn-danger delete-user-btn" id="delete" type="submit" {{ auth()->user()->id == $user->id ? 'disabled' : '' }}><i class="ti ti-trash"></i></button>
                                </span>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                pageLength: 5,
                scrollX: true,
                paging: true,
                searching: true,
                info: true,
                stateSave: true,
                lengthMenu: [5, 10, 25, 50, 100]
            });
            $('.dataTables_info, .dataTables_paginate').addClass('mt-4 mb-5');
            $('.dataTables_length').addClass('mb-4');
            $('#users-table').on('click', '.role-edit', function() {
                const user = $(this).data('user');
                const role = $(this).data('role');
                const url = $(this).data('href');
                let admin = role == 1 ? 'selected' : '';
                let student = role == 0 ? 'selected' : '';
                (async () => {
                    const {
                        value: inputValue
                    } = await swalCustom.fire({
                        title: 'Pilih role:',
                        html: `<p class="mb-2">${user}</p>` +
                            `<select id="role-select" class="form-select mb-2">` +
                            `<option ${admin} value="1">Admin</option>` +
                            `<option ${student} value="0">Student</option>` +
                            `</select>`,
                        focusConfirm: false,
                        showCancelButton: true,
                    });
                    if (inputValue) {
                        console.log('tet');
                        $.ajax({
                            url: url,
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                role: $('#role-select').val(),
                            },
                            success: function(response) {
                                console.log(response);
                                $(`td#${response['id']}`).html(response['badge']);
                                $(`button[data-id=${response['id']}]`).data("role", response['role']);
                                swalCustom.fire({
                                    icon: response['alert'],
                                    html: response['html']
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                                swalCustom.fire({
                                    title: status,
                                    icon: 'error',
                                    html: error,
                                });
                            }
                        });
                    }
                })();
            });
        });
    </script>
@endsection
