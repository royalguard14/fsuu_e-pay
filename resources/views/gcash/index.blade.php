@extends('layouts.master')

@section('header')
Gcash Information Management
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
                        <img src="{{ asset('storage/' . $gcash->qr_code) }}" alt="QR Code" width="50" height="50">
                        @else
                        N/A
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="openEditModal({{ $gcash->id }}, '{{ $gcash->account_name }}', '{{ $gcash->account_number }}', '{{ $gcash->qr_code }}')">Edit</button>
                        <form action="{{ route('gcash.destroy', $gcash->id) }}" method="POST" style="display:inline;">
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
                        <input type="file" name="qr_code" class="form-control">
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
        <form id="editGcashForm" method="POST" enctype="multipart/form-data">
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
                        <img id="edit_qr_code" src="" alt="QR Code" width="50" height="50">
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

<script>
    function openEditModal(id, accountName, accountNumber, qrCode) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_account_name').value = accountName;
        document.getElementById('edit_account_number').value = accountNumber;

        const qrCodeImg = document.getElementById('edit_qr_code');
        if (qrCode) {
            qrCodeImg.src = `/storage/${qrCode}`;
            qrCodeImg.style.display = 'block';
        } else {
            qrCodeImg.style.display = 'none';
        }

        const form = document.getElementById('editGcashForm');
        form.action = `/gcash/${id}`;

        $('#editGcashModal').modal('show');
    }
</script>

@endsection
