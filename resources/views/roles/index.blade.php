@extends('layouts.master')
@section('header')
Role Management
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
  <!-- Left col: Create Role -->
  <section class="col-lg-5 connectedSortable">
     <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add new Role</h3>
      </div>
      <div class="card-body">
       <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="role_name">Role Name</label>
            <input type="text" id="role_name" name="role_name" class="form-control" required>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success col-lg-12">Create Role</button>
    </form>
</div>
</div>
</section>

<!-- Right col: Role List -->
<section class="col-lg-7 connectedSortable">
 <div class="card">
    <div class="card-header">
      <h3 class="card-title">Role List</h3>
  </div>
  <div class="card-body">
      <table class="table table-head-fixed text-nowrap" id="example3">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                @if ($role->id == 1)
                    @continue
                @endif
                <tr>
                    <td>{{ $loop->iteration - 1 }}</td>
                    <td>{{ $role->role_name }}</td>
                    <td>

           <button type="button" class="btn btn-warning" data-role-id="{{ $role->id }}" data-name="{{ $role->role_name }}" onclick="openEditModal({{ $role->id }})">
    Edit
</button>


                    <button type="button" class="btn btn-info" onclick="openModuleModal({{ $role->id }})" data-name="{{ $role->role_name }}">
                        Manage Modules
                    </button>
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
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

   <div class="modal fade" id="modal-sm">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Role</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Role Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>

                    </div>

            
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>

               
                </form>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="moduleModal" tabindex="-1" aria-labelledby="moduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moduleModalLabel">Manage Modules</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="moduleList"></div> <!-- Modules will be populated here -->
            </div>
       
        </div>
    </div>
</div>

<script type="text/javascript">
    // Function to open the modal and load modules
    function openModuleModal(roleId) {
        // Store the roleId in the modal for later use
        $('#moduleModal').data('role-id', roleId);

        // Fetch modules for the role
        $.get('/roles/' + roleId + '/modules', function(response) {
            var modules = response.modules;
            var assignedModules = response.assignedModules;

            // Populate selectedModules with assignedModules initially
            selectedModules = [...assignedModules];  // Set the initially assigned modules

            var moduleListHtml = '';
            modules.forEach(function(module) {
                var checked = assignedModules.includes(module.id) ? 'checked' : '';
                moduleListHtml += `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input module-checkbox" data-role-id="${roleId}" data-module-id="${module.id}" value="${module.id}" ${checked}>
                        <label class="form-check-label" for="module_${module.id}">${module.name}</label>
                    </div>
                `;
            });

            // Populate the modal with the modules and checkboxes
            $('#moduleList').html(moduleListHtml);

            // Show the modal
            $('#moduleModal').modal('show');
        });
    }

    let selectedModules = [];

    // Automatically update when a checkbox is clicked (without needing to press "Save")
    $(document).on('click', '.module-checkbox', function() {
        var roleId = $(this).data('role-id');
        var moduleId = $(this).data('module-id');
        var isChecked = $(this).prop('checked');

        // Add moduleId to selectedModules if checked
        if (isChecked) {
            if (!selectedModules.includes(moduleId)) {
                selectedModules.push(moduleId);
            }
        } else {
            // Remove moduleId from selectedModules if unchecked
            var index = selectedModules.indexOf(moduleId);
            if (index !== -1) {
                selectedModules.splice(index, 1);
            }
        }

        // Convert selectedModules to integers to avoid string issues
        selectedModules = selectedModules.map(function(moduleId) {
            return parseInt(moduleId, 10);  // Ensure module IDs are integers
        });

        // Log the updated selectedModules array for debugging
        console.log('Selected Modules: ', selectedModules);

        // Send the updated array to the backend to update the role's modules
        $.ajax({
            url: '/roles/' + roleId + '/modules',  // Ensure this URL is correct for your API endpoint
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),  // CSRF token for security
                role_id: roleId,
                modules: selectedModules,  // Send the updated array of selected module IDs
            },
            success: function(response) {
                console.log('Modules updated successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error updating modules:', error);
            }
        });
    });

</script>

<script type="text/javascript">
    function openEditModal(roleId) {
        // Get the role name from the button's data-name attribute
        var roleName = $('button[data-role-id="' + roleId + '"]').data('name');

        // Set the roleId in the modal for later use
        $('#modal-sm').data('role-id', roleId);

        // Set the role name dynamically into the input field
        $('#name').val(roleName);  // Ensure this is setting the correct value

        // Show the modal
        $('#modal-sm').modal('show');
    }
</script>


@endsection
