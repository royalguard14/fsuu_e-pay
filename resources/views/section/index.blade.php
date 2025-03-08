@extends('layouts.master')

@section('header')
Section Management
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
            title: '{{ $errors->first() }}'
        });
    });
</script>
@endif

<!-- Main Content -->
<div class="row">
    <!-- Left col: Create Section -->
    <section class="col-lg-5 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New Section</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('section.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="section_name">Section Name</label>
                        <input type="text" id="section_name" name="section_name" class="form-control" required>
                    </div>
               
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success col-lg-12">Create Section</button>
            </div>
            </form>
        </div>
    </section>

    <!-- Right col: Section List -->
    <section class="col-lg-7 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Section List</h3>
            </div>
            <div class="card-body">
                <table class="table table-head-fixed text-nowrap" id="example3">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Section Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sections as $section)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $section->section_name }}</td>
                            <td>
                                <button type="button" class="btn btn-warning"
                                    data-section-id="{{ $section->id }}"
                                    data-name="{{ $section->section_name }}"
                                    onclick="openEditModal({{ $section->id }})">
                                    Edit
                                </button>

                                <form action="{{ route('section.destroy', $section) }}" method="POST"
                                    style="display:inline;">
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

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Section</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('section.update', $section ?? '') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="edit_section_id" name="section_id">

                    <div class="form-group">
                        <label for="edit_section_name">Section Name</label>
                        <input type="text" id="edit_section_name" name="section_name" class="form-control" required>
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

<script type="text/javascript">
    function openEditModal(sectionId) {
        var button = $('button[data-section-id="' + sectionId + '"]');
        $('#edit_section_id').val(sectionId);
        $('#edit_section_name').val(button.data('name'));
        $('#editSectionModal').modal('show');
    }
</script>

@endsection
