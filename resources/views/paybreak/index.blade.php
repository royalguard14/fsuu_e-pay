@extends('layouts.master')

@section('header')
Fee Breakdown Management
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

<!-- Button to open add fee breakdown modal -->
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addFeeModal">PARTICULARS (SY:{{ $currentYear->start }} - {{ $currentYear->end }})</button>

<!-- Fee Breakdown Table -->
<div class="card">
	<div class="card-header">Fee Breakdown List</div>
	<div class="card-body">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Grade Level</th>
					<th>Academic Year</th>
					<th>Tuition Fee</th>
					<th>Other Fees</th>
					<th>Total Fees</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				@foreach($feeBreakdowns as $fee)
				<tr>
					<td>{{ $fee->gradeLevel->level }}</td>
					<td>{{ $fee->academicYear->start }} - {{ $fee->academicYear->end }}</td>
					<td>₱{{ number_format($fee->tuition_fee, 2) }}</td>
					<td>₱{{ number_format(collect(json_decode($fee->other_fees, true))->sum(), 2) }}</td>
					<td>₱{{ number_format($fee->tuition_fee + collect(json_decode($fee->other_fees, true))->sum(), 2) }}</td>
					<td>




						@if($fee->academic_year_id === $currentYear->id)
						<button class="btn btn-warning btn-sm" onclick="openEditModal({{ $fee->id }}, {{ $fee->tuition_fee }}, '{{ $fee->other_fees }}')">Edit</button>
						@endif
						<form action="{{ route('fees.destroy', $fee->id) }}" method="POST" style="display:inline;">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
						</form>
					</td>









				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>


<!-- Add Fee Modal -->
<div class="modal fade" id="addFeeModal" tabindex="-1" aria-labelledby="addFeeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form action="{{ route('fees.store') }}" method="POST">
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addFeeModalLabel">Add Fee Breakdown</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
				</div>
				<div class="modal-body">
					{{-- Automatically set the current academic year --}}
					<input type="hidden" name="academic_year_id" value="{{ $currentYear->id }}">

<!-- Grade Level Selection -->
<div class="form-group">
    <label>Grade Level</label>
    <select name="grade_level_id" class="form-control" required onchange="updateFees(this)">
        <option value="" selected disabled>Select Grade Level</option> <!-- Default empty option -->
        @foreach($gradeLevels as $grade)
            <option value="{{ $grade->id }}" data-level="{{ strtolower($grade->level) }}"
                @if(in_array($grade->id, $usedGradeLevels)) disabled @endif>
                {{ $grade->level }}
            </option>
        @endforeach
    </select>
</div>


					{{-- Tuition Fee --}}
					<div class="form-group">
						<label>Tuition Fee</label>
						<input type="number" step="0.01" name="tuition_fee" class="form-control" value="9000" required>
					</div>

					{{-- Other Fees --}}
@php
    // Default fees for all grade levels except Kinder/Nursery
    $defaultFees = [
        'acea_fee' => 25, 'ceap_fee' => 25, 'computerization_fee' => 265,
        'dbes_fee' => 240, 'english_fee' => 130, 'filipino_fee' => 100,
        'foundation_day_fee' => 150, 'id_fee' => 150, 'library_fee' => 100,
        'light_water_fee' => 150, 'medical_dental_fee' => 70,
        'recognition_fee' => 100, 'registration_fee' => 50,
        'science_math_fee' => 150, 'science_lab_fee' => 200,
        'school_publication_fee' => 150, 'security_guard_fee' => 650,
        'socio_cultural_fee' => 20, 'sports_fee' => 50,
        'student_activity_fee' => 230, 'welfare_fund_fee' => 50,
        'support_personnel_fee' => 200, 'testing_materials_fee' => 200,
        'tle_fee' => 100
    ];

    // Fees for Kinder and Nursery only
    $specialFees = [
        'light_water_fee' => 1200,
        'security_guard_fee' => 650,
        'extra_curricular' => 350
    ];
@endphp

<!-- Fees Container -->
<!-- Fees Container (Initially Empty) -->
<div class="row" id="feeContainer"></div>

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
    function updateFees(select) {
        const selectedGrade = select.options[select.selectedIndex]?.getAttribute('data-level') || '';

        const specialFees = {
            light_water_fee: 1200,
            security_guard_fee: 650,
            extra_curricular: 350
        };

        const feeContainer = document.getElementById('feeContainer');
        feeContainer.innerHTML = ''; // Clear all fees initially

        if (!selectedGrade) return; // Do nothing if no grade is selected

        if (selectedGrade.includes('kinder') || selectedGrade.includes('nursery')) {
            // Display only Kinder/Nursery fees
            for (const [key, value] of Object.entries(specialFees)) {
                feeContainer.innerHTML += `
                    <div class="form-group col-lg-3 col-6">
                        <label style="font-size: .8rem; font-weight: bold;">${key.replace('_', ' ').toUpperCase()}</label>
                        <input type="number" step="0.01" name="other_fees[${key}]" class="form-control" value="${value}" required>
                    </div>
                `;
            }
        } else {
            // Display default fees for other grade levels
            const defaultFees = @json($defaultFees);
            for (const [key, value] of Object.entries(defaultFees)) {
                feeContainer.innerHTML += `
                    <div class="form-group col-lg-3 col-6">
                        <label style="font-size: .8rem; font-weight: bold;">${key.replace('_', ' ').toUpperCase()}</label>
                        <input type="number" step="0.01" name="other_fees[${key}]" class="form-control" value="${value}" required>
                    </div>
                `;
            }
        }
    }

    // Ensure the modal starts with an empty fee container
    $('#addFeeModal').on('show.bs.modal', function () {
        document.getElementById('feeContainer').innerHTML = ''; // Reset fees when modal opens
    });
</script>


<!-- Edit Fee Modal -->
<div class="modal fade" id="editFeeModal" tabindex="-1" aria-labelledby="editFeeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form id="editFeeForm" method="POST">
			@csrf
			@method('PUT')
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Fee Breakdown</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="edit_id">
					<div class="form-group">
						<label>Tuition Fee</label>
						<input type="number" step="0.01" name="tuition_fee" id="edit_tuition_fee" class="form-control" required>
					</div>
					<div class="row" id="edit_other_fees"></div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-warning" >Update</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
function openEditModal(id, tuitionFee, otherFees) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_tuition_fee').value = tuitionFee;

    const otherFeesParsed = JSON.parse(otherFees);
    let otherFeesHtml = '';
    for (const [key, value] of Object.entries(otherFeesParsed)) {
        otherFeesHtml += `
            <div class="form-group col-lg-3 col-6">
                <label style="font-size: 0.8rem; font-weight: bold;">${key.replace('_', ' ').toUpperCase()}</label>
                <input type="number" step="0.01" name="other_fees[${key}]" class="form-control" value="${value}" required>
            </div>
        `;
    }
    document.getElementById('edit_other_fees').innerHTML = otherFeesHtml;

    const form = document.getElementById('editFeeForm');
    form.action = `/fees/${id}`;

    $('#editFeeModal').modal('show');
}

</script>

@endsection
