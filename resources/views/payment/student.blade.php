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
             <div class="card-body text-center" id="gcashDetails">
                <p>Loading GCash details...</p>
            </div>


        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-success col-lg-12" onclick="openGcashModal()">Submit Payment</button>
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
                <li class="nav-item">
                    <a class="nav-link" id="gcash-transactions-tab" data-toggle="pill" href="#gcash-transactions" role="tab" aria-controls="gcash-transactions" aria-selected="false">GCash Transactions</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="walkin-transactions-tab" data-toggle="pill" href="#walkin-transactions" role="tab" aria-controls="walkin-transactions" aria-selected="false">Walk-in Transactions</a>
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
                        <th>Suggested Amount</th>
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
            <div class="tab-pane fade" id="gcash-transactions" role="tabpanel" aria-labelledby="gcash-transactions-tab">
               <table class="table table-striped" id="gcashtable">
                <thead>
                    <tr>
                        <th>Transaction Date</th>
                        <th>Amount</th>
                        <th>Reference Number</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="gcashTransactionTable">
                </tbody>
            </table>
        </div>


        <div class="tab-pane fade" id="walkin-transactions" role="tabpanel" aria-labelledby="walkin-transactions-tab">
           <table class="table table-striped" id="walkintable">
            <thead>
                <tr>
                    <th>Transaction Date</th>
                    <th>Amount</th>
                    <th>Reference Number</th>

                </tr>
            </thead>
            <tbody id="walkinTransactionTable">
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</section>
</div>


<div class="modal fade" id="gcashPaymentModal">
    <div class="modal-dialog">
      <div class="modal-content bg-primary">
        <div class="modal-header">
          <h4 class="modal-title">Gcash Payment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">

        <form action="{{ route('payment.spay-via-gcash') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">


                <div class="form-group mb-3">
                    <label for="amount" class="form-label">Amount to Pay</label>
                    <input type="number" name="amount" id="amount" class="form-control" min="1" required>
                </div>

                <div class="form-group mb-3">
                    <label for="reference_number" class="form-label">GCash Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number" class="form-control" maxlength="50" required>
                </div>

                <div class="form-group mb-3">
                    <label for="receipt" class="form-label">Upload Receipt (JPEG, PNG, JPG)</label>
                    <input type="file" name="receipt" id="receipt" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                </div>
            </div>


        </div>
        <div class="modal-footer">

            <button type="submit" class="btn btn-success">Submit Payment</button>
        </div>
    </form>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->




<div class="modal fade" id="receiptModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-primary text-white">
            <div class="modal-header">
                <h4 class="modal-title">Reference Number: <span id="modalReferenceNumber"></span></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <img id="modalReceiptImage" alt="Receipt" style="width:100%; max-height:400px; object-fit:contain; display:none;">
                <p id="noReceiptMessage" style="display:none;">No receipt available</p>

                <!-- Optional Reason Section -->
                <p id="modalReasonRow" style="display:none;" class="mt-3 text-left">
                    <strong>Reason for Rejection:</strong><br>
                    <span id="modalReason"></span>
                </p>

     
            </div>


        </div>
    </div>
</div>






<script>
    document.addEventListener('DOMContentLoaded', function() {
      let userId = {{ auth()->user()->id }};

      loadPaymentDetails(userId);
      loadPaymentHistory(userId);
      loadFeeBreakdown(userId)
      loadgcashtransaction();
      getActiveGcash();
      loadwalkintransaction();
  });
</script>
<script>
    function payViaGcashs() {
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


           document.getElementById('totalPaid').innerText = `₱${data.totalPaid ?? 0}`;
           document.getElementById('totalBalance').innerText = `₱${data.totalBalance ?? 0}`;
           document.getElementById('suggestedAmount').innerText = `₱${data.suggestedAmount ?? 0}`;

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
                //tableBody.innerHTML = '<tr><td colspan="2" class="text-center">No fee breakdown available. ${message} </td></tr>';
                tableBody.innerHTML = `<tr><td colspan="2" class="text-center">${data.message}</td></tr>`;

            }
        })
        .catch(error => console.error('Error loading fee breakdown:', error));
    }
</script>
<script type="text/javascript">
   function loadgcashtransaction() {
    fetch(`/gcash/mygcashtrans`)
    .then(response => response.json())
    .then(data => {
        let tableBody = document.getElementById('gcashTransactionTable');
        tableBody.innerHTML = '';
        if (data.gcashTransaction.length > 0) {
            data.gcashTransaction.forEach(transaction => {
                    // Format the date like 'March 9, 2025'
                let date = transaction.created_at
                ? new Date(transaction.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                })
                : 'N/A';
let formattedStatus = transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1);

let row = `
    <tr>
        <td>${date}</td>
        <td>₱${parseFloat(transaction.amount).toFixed(2)}</td>
        <td>${transaction.reference_number || 'N/A'}</td>
        <td>
            <a 
                class="text-${transaction.status === 'rejected' ? 'danger' : 'success'} text-decoration-none" 
                href="#" 
                onclick="showReceiptModal(
                    '${transaction.reference_number}', 
                    '${transaction.receipt}', 
                    '${transaction.status}', 
                    \`${transaction.reason || ''}\`
                )"
            >${formattedStatus}</a>
        </td>
    </tr>
`;


                tableBody.innerHTML += row;
            });
            $('#gcashtable').DataTable().destroy();
            $('#gcashtable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No transactions found.</td></tr>';
        }
    })
    .catch(error => console.error('Error loading gcash transactions:', error));
}
</script>

<script type="text/javascript">
    function showReceiptModal(referenceNumber, receiptPath, status, reason = '') {
    document.getElementById('modalReferenceNumber').textContent = referenceNumber;

    const receiptImg = document.getElementById('modalReceiptImage');
    const noReceiptMsg = document.getElementById('noReceiptMessage');

    if (receiptPath) {
        receiptImg.src = `/storage/${receiptPath}`;
        receiptImg.style.display = 'block';
        noReceiptMsg.style.display = 'none';
    } else {
        receiptImg.style.display = 'none';
        noReceiptMsg.style.display = 'block';
    }

    // Show reason if rejected
    if (status === 'rejected') {
        document.getElementById('modalReasonRow').style.display = 'block';
        document.getElementById('modalReason').textContent = reason || 'No reason provided.';
    } else {
        document.getElementById('modalReasonRow').style.display = 'none';
        document.getElementById('modalReason').textContent = '';
    }

    $('#receiptModal').modal('show');
}

</script>

<script type="text/javascript">
    function loadwalkintransaction() {
        fetch(`/gcash/mywalkintrans`)
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById('walkinTransactionTable');
            tableBody.innerHTML = '';

            if (data.walkInPayments.length > 0) {
                data.walkInPayments.forEach(transaction => {
                // Format the date like 'March 9, 2025'
                    let date = transaction.created_at
                    ? new Date(transaction.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })
                    : 'N/A';

                    let row = `
                    <tr>
                        <td>${date}</td>
                        <td>₱${parseFloat(transaction.amount_paid).toFixed(2)}</td>
                        <td>${transaction.reference_number || 'N/A'}</td>
                    </tr>
                    `;
                    tableBody.innerHTML += row;
                });

            // Reinitialize DataTable
                $('#walkintable').DataTable().destroy();
                $('#walkintable').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No transactions found.</td></tr>';
            }
        })
        .catch(error => console.error('Error loading walk-in transactions:', error));
    }
</script>



<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', getActiveGcash);

    function getActiveGcash() {
        fetch(`/gcash/getActive`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                    const gcash = data[0]; // Get the first GCash account
                    const gcashDetails = `
                        <img src="/storage/${gcash.qr_code}" alt="GCash QR Code" class="img-fluid mb-3" style="max-width: 200px; border: 1px solid #ddd; border-radius: 8px;">
                        <h5>${gcash.account_name}</h5>
                        <p>${gcash.account_number}</p>
                    `;
                    document.getElementById('gcashDetails').innerHTML = gcashDetails;
                } else {
                    document.getElementById('gcashDetails').innerHTML = '<p>No active GCash account found.</p>';
                }
            })
        .catch(error => {
            console.error('Error loading GCash details:', error);
            document.getElementById('gcashDetails').innerHTML = '<p>Failed to load GCash details.</p>';
        });
    }
</script>


<script type="text/javascript">
    function openGcashModal(gcashId) {
       $('#gcashPaymentModal').modal('show');
   }

</script>
@endsection