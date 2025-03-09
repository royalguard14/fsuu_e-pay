@extends('layouts.master')
@section('header')
Payment Management
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

<div class="row">
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

    <section class="col-lg-7 connectedSortable">
        <div class="card card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="payment-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="payment-history-tab" data-toggle="pill" href="#payment-history" role="tab" aria-controls="payment-history" aria-selected="true">Payment History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="fee-breakdown-tab" data-toggle="pill" href="#fee-breakdown" role="tab" aria-controls="fee-breakdown" aria-selected="false">Particular</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="payment-details-tab" data-toggle="pill" href="#payment-details" role="tab" aria-controls="payment-details" aria-selected="false">Payment Details</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">

                <div class="tab-content" id="payment-tab-content">
                    <div class="tab-pane fade show active" id="payment-history" role="tabpanel" aria-labelledby="payment-history-tab">
                            <table class="table table-head-fixed text-nowrap table-striped" id="paymentHistoryTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Reference No.</th>
                    </tr>
                </thead>
                <tbody id="paymentHistory"></tbody>
            </table>
                    </div>
                    <div class="tab-pane fade" id="fee-breakdown" role="tabpanel" aria-labelledby="fee-breakdown-tab">
                           <table class="table table-head-fixed text-nowrap table-striped" id="feeBreakdownTable">
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody id="feeBreakdown"></tbody>
        </table>

                            </div>
                    <div class="tab-pane fade" id="payment-details" role="tabpanel" aria-labelledby="payment-details-tab">
                        <table class="table table-bordered">
                            <tr>
                                <th>Total Paid</th>
                                <td id="totalPaid"></td>
                            </tr>
                            <tr>
                                <th>Total Balance</th>
                                <td id="totalBalance"></td>
                            </tr>
                            <tr>
                                <th>Suggested Amount (Unpaid Balance)</th>
                                <td id="suggestedAmount"></td>
                            </tr>
                        </table>
                        <h5>Monthly Payment Status</h5>
                        <table class="table table-striped" id="monthlyPaymentsTable">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Amount Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyPayments">




                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
  // let userId = {{ auth()->user()->id }};

            let userId = 4;
loadPaymentDetails(userId);
loadPaymentHistory(userId);
loadFeeBreakdown(userId)
         
        });
    </script>


<script>


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
            location.reload();
        } else {
            Toast.fire({
                icon: 'error',
                title: data.message
            });
        }
    })
    .catch(error => console.error('Error submitting GCash payment:', error));
}


</script>

    <script type="text/javascript">
        function loadPaymentDetails(studentId) {
            fetch(`/payments/${studentId}/payment-details`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalPaid').innerText = `₱${data.totalPaid}`;
                document.getElementById('totalBalance').innerText = `₱${data.totalBalance}`;
                document.getElementById('suggestedAmount').innerText = `₱${data.suggestedAmount}`;
                const tableBody = document.querySelector('#monthlyPaymentsTable tbody');
            tableBody.innerHTML = ''; // Clear existing rows
            data.monthlyPayments.forEach(payment => {
                const row = `
                    <tr>
                        <td>${payment.month}</td>
                        <td>₱${payment.amount}</td>
                        <td>${payment.status}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
            .catch(error => console.error('Error fetching payment details:', error));
        }
    </script>

<script type="text/javascript">
    function loadPaymentHistory(studentId) {
        fetch(`/payments/${studentId}/payments`)
            .then(response => response.json())
            .then(data => {
                let tableBody = document.getElementById('paymentHistory');
                tableBody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(payment => {
                        // Format the date like 'March 9, 2025'
                        let date = new Date(payment.payment_date);
                        let formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        let row = `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>₱${parseFloat(payment.amount_paid).toFixed(2)}</td>
                                <td>${payment.reference_number || 'N/A'}</td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });

                    $('#paymentHistoryTable').DataTable().destroy();
                    $('#paymentHistoryTable').DataTable({
                        "paging": true,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": false,
                        "info": false,
                        "autoWidth": false,
                        "responsive": true,
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No payment history available.</td></tr>';
                }
            })
            .catch(error => console.error('Error loading payment history:', error));
    }
</script>


    <script type="text/javascript">
        function loadFeeBreakdown(studentId) {
    fetch(`/payments/${studentId}/fee-breakdown`)
    .then(response => response.json())
    .then(data => {
        let tableBody = document.getElementById('feeBreakdown');
        tableBody.innerHTML = '';
        if (data.length > 0) {
            let totalAmount = 0;
                // Prepare fee rows and calculate total
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
                // Add the fee rows to the table
            tableBody.innerHTML = rows;
                // Total row added separately with a unique ID
            let totalRow = `
                    <tr id="totalRow">
                        <td><strong>Total</strong></td>
                        <td><strong>₱${totalAmount.toFixed(2)}</strong></td>
                    </tr>
            `;
            tableBody.innerHTML += totalRow;
                // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#feeBreakdownTable')) {
                $('#feeBreakdownTable').DataTable().destroy();
            }
                // Reinitialize DataTable and ensure the total row stays at the bottom
            $('#feeBreakdownTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "drawCallback": 
                function () {
                        // Move the total row to the end after DataTable redraws
                    $('#feeBreakdownTable tbody').append($('#totalRow'));
                }
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="2" class="text-center">No fee breakdown available.</td></tr>';
        }
    })
    .catch(error => console.error('Error loading fee breakdown:', error));
}
    </script>


@endsection
