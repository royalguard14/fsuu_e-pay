@extends('layouts.master')

@section('header')
Academic Year Management
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

<div class="row">
    <!-- Form to create academic year -->
    <section class="col-lg-5 connectedSortable">
        <div class="card">
            <div class="card-header">Add New Academic Year</div>
            <div class="card-body">
                <form action="{{ route('academic.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="start">Start Year</label>
                        <input type="number" name="start" class="form-control" min="{{ now()->year }}" required>
                    </div>

                    <button type="submit" class="btn btn-success mt-3 col-12">Create Academic Year</button>
                </form>
            </div>
        </div>
    </section>

    <!-- List of academic years -->
    <section class="col-lg-7 connectedSortable">
        <div class="card">
            <div class="card-header">Academic Years</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Start Year</th>
                            <th>End Year</th>
                            <th>Current</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($years as $year)
                        <tr>
                            <td>{{ $year->start }}</td>
                            <td>{{ $year->end }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input"
                                           id="current_year_{{ $year->id }}"
                                           onchange="setCurrentYear({{ $year->id }})"
                                           {{ $year->current ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('academic.destroy', $year) }}" method="POST" style="display:inline;">
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

<script>
  function setCurrentYear(yearId) {
    fetch(`/academic/${yearId}/set-current`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Toast.fire({
                icon: data.icon,
                title: data.success
            });

            // Uncheck all other checkboxes
            document.querySelectorAll('.form-check-input').forEach(input => {
                input.checked = false;
            });

            // Check only the current one
            document.getElementById(`current_year_${yearId}`).checked = true;
        } else if (data.error) {
            Toast.fire({
                icon: data.icon,
                title: data.error
            });
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        Toast.fire({
            icon: 'error',
            title: 'Something went wrong!'
        });

        // Revert the checkbox if there’s an error
        const checkbox = document.getElementById(`current_year_${yearId}`);
        checkbox.checked = !checkbox.checked;
    });
}
</script>
@endsection
