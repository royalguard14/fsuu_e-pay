@extends('layouts.master')
@section('header')
Enrollees Management
@endsection
@section('heading')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
@endsection
@section('scripts')

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
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
        <table class="table table-head-fixed text-nowrap" id="example3">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Full Name</th>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Enrolled Date</th>
                    <th>Actions</th>
                    <th>isActive</th>
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
                       <button class="btn btn-warning btn-sm" onclick="openTransferModal({{ $student->section->id }}, {{ $student->gradeLevel->id }}, {{ $student->id }})">Transfer</button>
                    </td>

                        <td>
        <!-- Toggle Switch -->
        <input type="checkbox" class="toggle-status" 
               data-user-id="{{ $student->user->id }}" 
               {{ $student->user->isActive ? 'checked' : '' }}>
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
                    <select name="user_id" id="student" class="form-control select2" required>
                        <option value="">Search Student...</option>
                        @foreach($unenrolledStudents as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->profile->firstname }} {{ $student->profile->lastname }}
                        </option>
                        @endforeach
                    </select>
                </div>
             <script>
    $(document).ready(function () {
        // Initialize Select2 when the modal is opened
        $('#enrollModal').on('shown.bs.modal', function () {
            $('#student').select2({
                dropdownParent: $('#enrollModal'), // Ensures Select2 works inside a Bootstrap modal
                placeholder: "Search Student...",
                allowClear: true,
                width: '100%'
            });
        });
    });
</script>

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


<div class="modal fade" id="transferModel" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('enrollees.transfer') }}" method="POST">
                @csrf
                <input type="hidden" name="enrollment_id" id="enrollment_id"> <!-- Hidden input for enrollment ID -->
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sections">Select Section</label>
                        <select name="section_id" id="sections" class="form-control" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Transfer</button>
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
        fetch(`/enrollees/get-sections/${gradeId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            sectionDropdown.innerHTML = '<option value="">Select Section</option>';
            data.sections.forEach(section => {
                sectionDropdown.innerHTML += `<option value="${section.id}">${section.section_name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading sections:', error);
            sectionDropdown.innerHTML = `<option value="">Failed to load sections (${error.message})</option>`;
        });
    }
</script>



<script type="text/javascript">
   function openTransferModal(secID, gradeID, enrolleesID) {
        const sectionDropdowns = document.getElementById('sections');
        document.getElementById('enrollment_id').value = enrolleesID; // Set enrollment ID in the form

        fetch(`/enrollees/get-sections/${gradeID}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                sectionDropdowns.innerHTML = '<option value="">Select Section</option>';
                
                data.sections.forEach(section => {
                    const isSelected = section.id == secID ? 'selected' : '';
                    sectionDropdowns.innerHTML += `<option value="${section.id}" ${isSelected}>${section.section_name}</option>`;
                });

                // Show the modal after updating the options
                $('#transferModel').modal('show');
            })
            .catch(error => {
                console.error('Error loading sections:', error);
                sectionDropdowns.innerHTML = `<option value="">Failed to load sections (${error.message})</option>`;
            });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-status').forEach(switchButton => {
            switchButton.addEventListener('change', function () {
                let userId = this.getAttribute('data-user-id');
                let isActive = this.checked ? 1 : 0;

                fetch(`/users/${userId}/toggle-active`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ isActive: isActive })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('Failed to update status.');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>


@endsection