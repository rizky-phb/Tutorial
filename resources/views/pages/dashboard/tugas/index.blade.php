@extends('pages.dashboard.layouts.main')
@section('head-script')
  <!-- DataTables -->
  <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('main')
  <div class="d-flex justify-content-between flex-md-nowrap align-items-center border-bottom mb-3 flex-wrap pb-2 pt-3">
    <h3>Tugas</h3>
    <a class="btn btn-sm btn-primary" href="{{ route('tugas.create') }}">
      <i class="ti ti-pencil-plus"></i> Add a tugas
    </a>
  </div>
  <table class="table-striped table-bordered w-100 table" id="tugas-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Cover</th>
        <th>Status</th>
        <th>Name</th>
        <th>Mata Pelajaran</th>
        <th>Favorite</th>
        <th>Added by</th>
        <th>Last Edited by</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($tugas as $t)
        @php
          $status = $t->draft ? 'Draft' : 'Published';
          $background = $t->draft ? 'warning' : 'success';
        @endphp
        <tr>
          <td>{{ $t->id }}</td>
          <td><img class="img-fluid rounded" id="" src="{{ $t->cover }}" alt="cover preview" style="
                        width: 150px;
                        height: auto;
                        object-fit: cover;
                        aspect-ratio: 16 / 9;">
          </td>
          <td>
            <span class="badge text-bg-{{ $background }}" data-bs-toggle="tooltip" data-bs-title="{{ $status == 'Draft' ? 'Tugas ini masih draft, publish tugas ini agar bisa diakses oleh user' : 'Tugas ini sudah bisa diakses oleh user' }}">{{ $status }}</span>
          </td>
          <td>{{ $t->title }}</td>
          <td>{{ $t->category->name }}</td>
          <td>{{ $t->favorite }}</td>
          <td>{{ $t->added_by }}<br>({{ $t->created_at }})</td>
          <td>{{ $t->last_edited_by }}<br>({{ $t->updated_at }})</td>
          <td class="text-right">
            <div class="d-grid d-flex gap-2">
              <a class="btn btn-sm btn-warning" id="detail" data-bs-toggle="tooltip" data-bs-title="View this tugas's detail" href="{{ route('tugas.show', $t->slug) }}"><i class="ti ti-eye"></i></a>
              <a class="btn btn-sm btn-primary" id="edit" data-bs-toggle="tooltip" data-bs-title="Edit this tugas" href="{{ route('tugas.edit', $t->slug) }}"><i class="ti ti-edit"></i></a>
              <form action="{{ route('tugas.destroy', $t->slug) }}" method="post">
                @csrf
                @method('delete')
                <button class="btn btn-sm btn-danger delete-tugas-btn" id="delete" data-bs-toggle="tooltip" data-bs-title="Delete this tugas" type="submit"><i class="ti ti-trash"></i></button>
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
      $('#tugas-table').DataTable({
        pageLength: 5,
        scrollX: true,
        paging: true,
        searching: true,
        info: true,
        stateSave: true,
        lengthMenu: [5, 10, 25, 50, 100],
      });
      $('.dataTables_info, .dataTables_paginate').addClass('mt-4 mb-5');
      $('.dataTables_length').addClass('mb-4');
    })
  </script>
@endsection
