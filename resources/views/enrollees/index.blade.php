@extends('layouts.master')

@section('header')
Enrollees Management
@endsection

@section('content')

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

<!-- Button to open enroll modal -->
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#enrollModal">Enroll Student</button>

<!-- Enrolled students table -->
<div class="card">
    <div class="card-header">Enrolled Students for {{ $currentYear->start }}-{{ $currentYear->end }}</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Full Name</th>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Enrolled Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
@foreach($enrolledStudents as $student)
<tr>
    <td>{{ $student->user->profile->lrn ?? 'N/A' }}</td>
    <td>{{ $student->user->profile->firstname ?? 'N/A' }} {{ $student->user->profile->lastname ?? 'N/A' }}</td>
    <td>{{ $student->gradeLevel->level ?? 'N/A' }}</td>
    <td>{{ $student->section->section_name ?? 'N/A' }}</td>
    <td>{{ $student->enrollment_date }}</td>
    <td>
        <button class="btn btn-warning btn-sm" onclick="openTransferModal({{ $student->id }})">Transfer</button>
    </td>
</tr>
@endforeach
            </tbody>
        </table>
    </div>
</div>







<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollModalLabel">Enroll Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('enrollees.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="student">Select Student</label>
                        <select name="user_id" id="student" class="form-control" required>
                            @foreach($unenrolledStudents as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->profile->firstname }} {{ $student->profile->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="grade">Select Grade Level</label>
                        <select name="grade_level_id" id="grade" class="form-control" onchange="loadSections()" required>
                            <option value="">Select Grade</option>
                            @foreach($gradeLevels as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="section">Select Section</label>
                        <select name="section_id" id="section" class="form-control" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Enroll</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="text/javascript">
    function loadSections() {
    const gradeId = document.getElementById('grade').value;
    const sectionDropdown = document.getElementById('section');

    sectionDropdown.innerHTML = '<option value="">Loading...</option>';

    fetch(`enrollees/get-sections/${gradeId}`)
        .then(response => response.json())
        .then(data => {
            sectionDropdown.innerHTML = '<option value="">Select Section</option>';
            data.sections.forEach(section => {
                sectionDropdown.innerHTML += `<option value="${section.id}">${section.section_name}</option>`;
            });
        })
        .catch(() => {
            sectionDropdown.innerHTML = '<option value="">Failed to load sections</option>';
        });
}

</script>





@endsection
