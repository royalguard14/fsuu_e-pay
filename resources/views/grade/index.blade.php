@extends('layouts.master')
@section('header')
Grade Management
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
  <!-- Left col: Create Grade -->
  <section class="col-lg-5 connectedSortable">
     <div class="card">
        <div class="card-header">
          <h3 class="card-title">Add New Grade</h3>
      </div>
      <div class="card-body">
       <form action="{{ route('grade.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="level">Grade Name</label>
            <input type="text" id="level" name="grade_name" class="form-control" required>
        </div>
      </div>
      <div class="card-footer">
          <button type="submit" class="btn btn-success col-lg-12">Create Grade</button>
      </form>
     </div>
    </div>
  </section>

  <!-- Right col: Grade List -->
  <section class="col-lg-7 connectedSortable">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Grade List</h3>
      </div>
      <div class="card-body">
          <table class="table table-head-fixed text-nowrap" id="example3">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Grade Name</th>
                   <th>Sections</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades as $grade)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $grade->level }}</td>
                      <td>
    @php
        $sectionIds = is_array($grade->section_ids) ? $grade->section_ids : json_decode($grade->section_ids, true);
        $sectionNames = \App\Models\Section::whereIn('id', $sectionIds)->pluck('section_name')->toArray();
    @endphp
    {{ implode(', ', $sectionNames) }}
</td>


                        <td>
                            <button type="button" class="btn btn-warning" 
                                data-grade-id="{{ $grade->id }}" 
                                data-name="{{ $grade->level }}" 
                                onclick="openEditModal({{ $grade->id }})">
                                Edit
                            </button>

{{-- <button type="button" class="btn btn-info" onclick="openSectionModal({{ $grade->id }})" data-sections="{{ json_encode($grade->section_ids) }}">
    Manage Sections
</button>
 --}}

 <button type="button" 
        class="btn btn-info" 
        data-grade-id="{{ $grade->id }}" 
        data-sections='@json($grade->section_ids)' 
        onclick="openSectionModal({{ $grade->id }})">
    Manage Sections
</button>


                            <form action="{{ route('grade.destroy', $grade) }}" method="POST" style="display:inline;">
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

<!-- Edit Grade Modal -->
<div class="modal fade" id="editGradeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Grade</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('grade.update', $grade ?? '') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="edit_grade_id" name="grade_id">

                    <div class="form-group">
                        <label for="edit_level">Grade Name</label>
                        <input type="text" id="edit_level" name="level" class="form-control" required>
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

<!-- Manage Sections Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Sections</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="sectionList"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function openEditModal(gradeId) {
        var button = $('button[data-grade-id="' + gradeId + '"]');
        $('#edit_grade_id').val(gradeId);
        $('#edit_level').val(button.data('name'));
        $('#editGradeModal').modal('show');
    }

  

</script>
<script type="text/javascript">



function openSectionModal(gradeId) {
    $.get('/grade/' + gradeId + '/sections', function(response) {
        var sections = response.sections;
        var assignedSections = response.assignedSections;

        console.log('Assigned Sections:', assignedSections); // Debug here

        var sectionListHtml = '';
        sections.forEach(function(section) {
            var checked = assignedSections.includes(section.id) ? 'checked' : '';
            sectionListHtml += `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input section-checkbox" 
                           data-grade-id="${gradeId}" 
                           data-section-id="${section.id}" 
                           value="${section.id}" 
                           ${checked}>
                    <label class="form-check-label" for="section_${section.id}">${section.section_name}</label>
                </div>
            `;
        });

        $('#sectionList').html(sectionListHtml);
        $('#sectionModal').modal('show');
    });
}















let selectedSections = [];

$(document).on('click', '.section-checkbox', function() {
    var gradeId = $(this).data('grade-id');
    var sectionId = $(this).val();

    $.ajax({
        url: `/grade/${gradeId}/sections`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            section_id: sectionId
        },
        success: function(response) {
            console.log('Updated section IDs:', response.section_ids);
        },
        error: function(xhr, status, error) {
            console.error('Error updating sections:', error);
        }
    });
});


</script>
@endsection
