@extends('layouts.master')
@section('header')
Payment Management
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
    <!-- Left col: Pay via GCash -->
    <section class="col-lg-5 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pay via GCash</h3>
            </div>
            <div class="card-body">
                <form id="gcashPaymentForm">
                    @csrf
                    <div class="form-group">
                        <label for="amount">Amount to Pay</label>
                        <input type="number" name="amount" id="amount" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="reference_number">GCash Reference Number</label>
                        <input type="text" name="reference_number" id="reference_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (optional)</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-success col-lg-12" onclick="payViaGcash()">Submit Payment</button>
            </div>
        </div>
    </section>
    <!-- Right col: Payment History and Fee Breakdown -->
    <section class="col-lg-7 connectedSortable">
        <div class="card card-tabs">
          <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Payment History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Particular</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-one-tabContent">
          <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
            <table class="table table-head-fixed text-nowrap" id="example3">
                <thead>
                    <tr>
                        <th>Reference No.</th>
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->reference_number }}</td>
                        <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
   <table id="feeBreakdownTable" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Fee Type</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody id="feeBreakdown"></tbody>
</table>

    </div>
</div>
</div>
<!-- /.card -->
</section>
</div>
@endsection
@section('scripts')
<script>

function loadFeeBreakdown(studentId) {
    fetch(`/payments/sfee-breakdown`)
        .then(response => response.json())
        .then(data => {
            console.log('Fee breakdown response:', data); // Debugging line

            let tableBody = document.getElementById('feeBreakdown');
            tableBody.innerHTML = '';

            if (Array.isArray(data) && data.length > 0) {
                let totalAmount = 0;

                // Prepare fee breakdown rows
                let rows = data.map(fee => {
                    let amount = parseFloat(fee.amount);
                    totalAmount += amount;

                    return `
                        <tr>
                            <td>${fee.fee_type}</td>
                            <td>₱${amount.toFixed(2)}</td>
                        </tr>
                    `;
                }).join('');

                // Add fee rows to the table
                tableBody.innerHTML = rows;

                // Add the total row separately so DataTables won't reorder it
                let totalRow = `
                    <tr id="totalRow">
                        <td><strong>Total</strong></td>
                        <td><strong>₱${totalAmount.toFixed(2)}</strong></td>
                    </tr>
                `;
                tableBody.innerHTML += totalRow;

                // Destroy DataTable only if it exists
                if ($.fn.DataTable.isDataTable('#feeBreakdownTable')) {
                    $('#feeBreakdownTable').DataTable().destroy();
                }

                // Reinitialize DataTable and exclude the total row from sorting
                $('#feeBreakdownTable').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
                    "order": [],
                    "drawCallback": function () {
                        
                        $('#feeBreakdownTable tbody').append($('#totalRow'));
                    }
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="2" class="text-center">No fee breakdown available.</td></tr>';
            }
        })
        .catch(error => console.error('Error loading fee breakdown:', error));
}


    // Pay via GCash
    function payViaGcash() {
        let amount = document.getElementById('amount').value;
        let reference_number = document.getElementById('reference_number').value;
        let notes = document.getElementById('notes').value;
        fetch('/payments/gcash', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                amount,
                reference_number,
                notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
                location.reload(); // Refresh the page to update the tables
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.message
                });
            }
        })
        .catch(error => console.error('Error submitting GCash payment:', error));
    }
    // Auto-load fee breakdown when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        let studentId = {{ auth()->user()->id }};
        loadFeeBreakdown(studentId);
    });
</script>
@endsection