@extends('layouts.master')
@section('header')
Setting Management
@endsection
@section('content')

<!-- Toast Notifications -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toast.fire({
            icon: '{{ session('icon') }}',
            title: '{{ session('success') }}'
        });
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toast.fire({
            icon: 'error',
            title: '{{ $errors->first() }}'  // Show the first validation error
        });
    });
</script>
@endif

<!-- Main Content -->
<div class="row">

  <!-- Left Column: Create Setting -->
  <section class="col-lg-5 connectedSortable">
     <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add New Setting</h3>
      </div>
      <div class="card-body">
       <form action="{{ route('settings.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="function_desc">Function Description</label>
            <input type="text" id="function_desc" name="function_desc" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="function">Function Value</label>
            <input type="text" id="function" name="function" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type" class="form-control" required>
                <option value="backend">Backend</option>
                <option value="frontend">Frontend</option>
            </select>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success col-lg-12">Create Setting</button>
    </form>
</div>
</div>
</section>

<!-- Right Column: Settings List -->
<section class="col-lg-7 connectedSortable">
 <div class="card">
    <div class="card-header">
      <h3 class="card-title">Settings List</h3>
  </div>
  <div class="card-body">
      <table class="table table-head-fixed text-nowrap" id="example3">
        <thead>
            <tr>
                <th>No.</th>
                <th>Description</th>
                <th>Function</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settings as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->function_desc }}</td>
                    <td>{{ $data->function }}</td>
                    <td>
                        <span class="badge {{ $data->type == 'backend' ? 'bg-primary' : 'bg-success' }}">
                            {{ ucfirst($data->type) }}
                        </span>
                    </td>
                    <td>

                    <!-- Edit Button -->
                    <button type="button" class="btn btn-warning" data-id="{{ $data->id }}" 
                            data-desc="{{ $data->function_desc }}" data-function="{{ $data->function }}" 
                            data-type="{{ $data->type }}" onclick="openEditModal({{ $data->id }})">
                        Edit
                    </button>

                    <!-- Delete Button -->
                    <form action="{{ route('settings.destroy', $data) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
</section>

</div>

<!-- Edit Modal -->
<div class="modal fade" id="editSettingModal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Setting</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editSettingForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">

                <div class="form-group">
                    <label for="edit_function_desc">Function Description</label>
                    <input type="text" id="edit_function_desc" name="function_desc" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_function">Function Value</label>
                    <input type="text" id="edit_function" name="function" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_type">Type</label>
                    <select id="edit_type" name="type" class="form-control" required>
                        <option value="backend">Backend</option>
                        <option value="frontend">Frontend</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
    function openEditModal(id) {
    var setting = $('button[data-id="' + id + '"]');
    var desc = setting.data('desc');
    var func = setting.data('function');
    var type = setting.data('type');

    $('#edit_id').val(id);
    $('#edit_function_desc').val(desc);
    $('#edit_function').val(func);
    $('#edit_type').val(type);

    $('#editSettingModal').modal('show');

    $('#editSettingForm').attr('action', '/settings/' + id);
}

</script>
@endsection
