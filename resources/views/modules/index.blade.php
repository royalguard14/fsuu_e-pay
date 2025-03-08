@extends('layouts.master')
@section('header')
Modules Management
@endsection
@section('content')

<?php
// Path to the FontAwesome CSS file
$cssFilePath = public_path('plugins/fontawesome-free/css/all.min.css');

// Read the CSS content
$cssContent = file_get_contents($cssFilePath);

// Extract all .fa-* class names
preg_match_all('/\.fa-([a-zA-Z0-9-]+):before/', $cssContent, $matches);

// Generate the <option> list
$options = '';
foreach ($matches[1] as $icon) {
    // $options .= '<option value="fa-' . $icon . '">&#x' . dechex(mt_rand(61440, 62191)) . '; fa-' . $icon . '</option>' . PHP_EOL;


    $options .= '<option value="fa-' . $icon . '" data-icon="fas fa-' . $icon . '">
    <i class="fas fa-' . $icon . '"></i>' . $icon . '
    </option>' . PHP_EOL;

}


?>




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
    <section class="col-lg-6 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New Module</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('modules.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Module Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Module Name</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                        </div>

                        <!-- Icon Select -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="icon">Icon (Optional)</label>
                                <select id="icon" name="icon" class="form-control select2">
                                    <option value="">-- Select Icon --</option>
                                    {!! $options !!}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- URL -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="url">URL (Optional)</label>
                                <input type="text" id="url" name="url" class="form-control">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-success col-lg-6">Create Module</button>
                </form>
            </div>
        </div>
    </section>
    <script>
        document.getElementById('icon').addEventListener('change', function() {
            this.style.fontFamily = 'FontAwesome';
        });
    </script>
<!-- FontAwesome Preview -->
<script>
    document.getElementById('icon').addEventListener('change', function() {
        this.style.fontFamily = 'FontAwesome';
    });
</script>

<!-- Right col: Role List -->
<section class="col-lg-6 connectedSortable">
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
                <th>Route</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($modules as $data)

            <tr>
                <td>{{ $loop->iteration + 0 }}</td>
                <td>{{ $data->name }}</td>
                <td>{{ $data->url }}</td>
                <td>

        <button type="button" class="btn btn-warning" 
                onclick="openEditModal({{ $data->id }})"
                data-id="{{ $data->id }}"
                data-name="{{ $data->name }}"
                data-icon="{{ $data->icon }}"
                data-url="{{ $data->url }}"
                data-description="{{ $data->description }}">
            Edit
        </button>


                <form action="{{ route('modules.destroy', $data) }}" method="POST" style="display:inline;">
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

<div class="modal fade" id="editModuleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Module</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editModuleForm" method="POST" action="{{ route('modules.update', $data) }}">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="module_id" id="editModuleId">
                    
                    <div class="form-group">
                        <label for="edit_name">Module Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_icon">Icon</label>
                        <select name="icon" id="edit_icon" class="form-control">
                            {!! $options !!}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_url">URL</label>
                        <input type="text" id="edit_url" name="url" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    function openEditModal(moduleId) {
    var button = $('button[data-id="' + moduleId + '"]');

    $('#editModuleId').val(button.data('id'));
    $('#edit_name').val(button.data('name'));
    $('#edit_icon').val(button.data('icon'));
    $('#edit_url').val(button.data('url'));
    $('#edit_description').val(button.data('description'));

    $('#editModuleModal').modal('show');
}

</script>


@endsection
