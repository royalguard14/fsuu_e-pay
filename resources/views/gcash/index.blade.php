@extends('layouts.master')

@section('header')
Gcash Information Management
@endsection

@section('content')

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toast.fire({
            icon: '{{ session('icon') ?? 'success' }}',
            title: '{{ session('success') }}'
        });
    });
</script>
@endif




@section('style')
<style type="text/css">
    .qr-thumbnail {
        transition: transform 0.2s ease-in-out;
    }

    .qr-thumbnail:hover {
        transform: scale(7); /* Makes the image 3x bigger */
        z-index: 1000; /* Ensures it appears above other elements */
        position: relative;
    }
</style>
@endsection

<!-- Button to open add Gcash info modal -->
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addGcashModal">Add Gcash Info</button>

<!-- Gcash Information Table -->
<div class="card">
    <div class="card-header">Gcash Information List</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>QR Code</th>
                    <th>IsActive</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gcashInfos as $gcash)

                <tr>
                    <td>{{ $gcash->id }}</td>
                    <td>{{ $gcash->account_name }}</td>
                    <td>{{ $gcash->account_number }}</td>
                    <td>
                        @if($gcash->qr_code)
                        <img src="{{ asset('storage/' . $gcash->qr_code) }}" alt="QR Code" width="50" height="50" class="qr-thumbnail">
                        @else
                        N/A
                        @endif
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input"
                            id="current_gcash_{{ $gcash->id }}"
                            onchange="setActive({{ $gcash->id }})"
                            {{ $gcash->isActive ? 'checked' : '' }}>
                        </div>
                    </td>

                    <td>
                      <button class="btn btn-warning btn-sm"
                      onclick='openEditModal(@json($gcash))'>
                      Edit
                  </button>


                 <form action="{{ route('gcash.destroy', $gcash->id) }}" method="POST" style="display:inline;" 
      onsubmit="return confirmDelete(event, '{{ $gcash->id }}')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
</form>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
</div>

<!-- Add Gcash Modal -->
<div class="modal fade" id="addGcashModal" tabindex="-1" aria-labelledby="addGcashModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('gcash.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGcashModalLabel">Add Gcash Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" name="account_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>QR Code (optional)</label>
                        <input type="file" name="qr_code" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Gcash Modal -->
<div class="modal fade" id="editGcashModal" tabindex="-1" aria-labelledby="editGcashModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editGcashForm" method="POST" enctype="multipart/form-data"
        action="{{ route('gcash.update', $gcash->id ?? '') }}">
        @csrf
        @method('PUT')

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGcashModalLabel">Edit Gcash Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" name="account_name" id="edit_account_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" id="edit_account_number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>QR Code (optional)</label>
                    <input type="file" name="qr_code" class="form-control">
                </div>
                <div class="form-group">
                    <label>Current QR Code</label><br>
                    <img id="edit_qr_code" src="" alt="QR Code" width="100" height="100" style="border:1px solid #ddd;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Update</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
</div>
</div>



<script type="text/javascript">
    function openEditModal(gcashInfo) {
        document.getElementById('edit_id').value = gcashInfo.id;
        document.getElementById('edit_account_name').value = gcashInfo.account_name;
        document.getElementById('edit_account_number').value = gcashInfo.account_number;
        document.getElementById('edit_qr_code').src = gcashInfo.qr_code 
        ? `/storage/${gcashInfo.qr_code}` 
        : 'https://via.placeholder.com/100?text=No+QR+Code';
        $('#editGcashModal').modal('show');
    }

</script>

<script>
function setActive(gcashID) {
    fetch(`/gcash/${gcashID}/set-active`, {
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
            document.getElementById(`current_gcash_${gcashID}`).checked = true;
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
        const checkbox = document.getElementById(`current_gcash_${gcashID}`);
        checkbox.checked = !checkbox.checked;
    });
}


</script>

<script type="text/javascript">
    function confirmDelete(event, gcashId) {
    event.preventDefault(); // Stop the form from submitting immediately

    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit(); // Submit the form if confirmed
        }
    });
}

</script>

@endsection
