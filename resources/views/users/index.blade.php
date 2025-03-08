@extends('layouts.master')
@section('header')
User Management
@endsection
@section('content')

<!-- Check for Session Success -->
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
  <!-- Left col: Create User -->
  <section class="col-lg-5 connectedSortable">
     <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add new User</h3>
      </div>
      <div class="card-body">
       <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="role_id">Role</label>
            <select id="role_id" name="role_id" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success col-lg-12">Create User</button>
    </form>
</div>
</div>
</section>

<!-- Right col: User List -->
<section class="col-lg-7 connectedSortable">
 <div class="card">
    <div class="card-header">
      <h3 class="card-title">User List</h3>
  </div>
  <div class="card-body">
      <table class="table table-head-fixed text-nowrap" id="example3">
        <thead>
            <tr>
                <th>No.</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->role_name }}</td>
                    <td>
                        <!-- Edit Button -->
                        <button type="button" class="btn btn-warning" data-user-id="{{ $user->id }}" onclick="openEditModal({{ $user->id }})">
                            Edit
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
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

<!-- Edit User Modal -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal_{{ $user->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit User</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="username_{{ $user->id }}">Username</label>
                        <input type="text" id="username_{{ $user->id }}" name="username" class="form-control" value="{{ $user->username }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email_{{ $user->id }}">Email</label>
                        <input type="email" id="email_{{ $user->id }}" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id_{{ $user->id }}">Role</label>
                        <select id="role_id_{{ $user->id }}" name="role_id" class="form-control">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @if($role->id == $user->role_id) selected @endif>{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="isActive_{{ $user->id }}">Active</label>
                        <input type="checkbox" id="isActive_{{ $user->id }}" name="isActive" @if($user->isActive) checked @endif>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
// Open the Edit Modal
function openEditModal(userId) {
    $('#editUserModal_' + userId).modal('show');
}
</script>
@endsection
