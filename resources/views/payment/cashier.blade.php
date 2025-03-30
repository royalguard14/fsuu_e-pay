@extends('layouts.master')
@section('header')
Payment Management
@endsection
@section('style')
@section('content')
<!-- Toast Notifications -->
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

    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', 
            function() {
                Toast.fire({
                    icon: 'error',
                    title: '{{ $errors->first() }}'
                });
            });
        </script>
        @endif
        <div class="row">
            <!-- Left Side: Student Search and Payment History -->
            <section class="col-lg-5 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Search Student</h3>
                    </div>
                    <div class="card-body">
                        <input type="text" id="studentSearch" class="form-control" placeholder="Search by LRN, Name, or ID" onkeyup="searchStudent()">
                        <div id="suggestions" class="list-group" style="display: none; position: absolute; z-index: 1000; width: 100%;"></div>
                    </div>
                    <div class="card-footer">
                        <button type="button" id="proceedToPayment" class="btn btn-success col-lg-12" onclick="processPayment()" disabled>Proceed to Payment</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">GCash Notification</h3>
                    </div>
                    <div class="card-body">
                      <table class="table table-head-fixed text-nowrap table-striped" id="pendingGcashTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Reference No.</th>
                             
                            </tr>
                        </thead>
                        <tbody id="pendingGcash"></tbody>
                    </table>
                </div>
            </div>
        </section>
        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Enter Payment Amount</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="number" id="paymentAmount" class="form-control" placeholder="Enter amount to pay" min="1" step="0.01">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitPayment()">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Side: Fee Breakdown -->
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
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-details-tab" data-toggle="pill" href="#custom-tabs-one-details" role="tab" aria-controls="custom-tabs-one-details" aria-selected="false">Payment Details</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-one-tabContent">
              <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
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
            <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
              <div id="previousBalance"></div>
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
        <div class="tab-pane fade" id="custom-tabs-one-details" role="tabpanel" aria-labelledby="custom-tabs-one-details-tab">
           <div id="paymentSummary">
            <h5>Total Amount Paid: <span id="totalPaid">₱0.00</span></h5>
            <h5>Total Balance: <span id="totalBalance">₱0.00</span></h5>
            <h5>Suggested Amount to Pay: <span id="suggestedAmount">₱0.00</span></h5>
        </div>
        <table class="table table-striped" id="monthlyPaymentsTable">
            <thead>
                <tr>
                    <th>Month & Year</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
</div>
<!-- /.card -->
</section>
</div>

 <div class="modal fade" id="receiptModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-primary">
            <div class="modal-header">
                <h4 class="modal-title">Reference Number: <span id="modalReferenceNumber"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalReceiptImage" alt="Receipt" style="width:100%; max-height:400px; object-fit:contain; display:none;">
                <p id="noReceiptMessage" style="display:none;">No receipt available</p>
                <p>
                    <strong>Sender:</strong> <span id="modalSender"></span><br>
                    <strong>Receiver:</strong> <span id="modalReceiver"></span>
                </p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-light" onclick="status('rejected')">Reject</button>
                <button type="button" class="btn btn-outline-light" onclick="status('approved')">Approve</button>
            </div>
        </div>
    </div>
</div>


<!-- JavaScript Section -->
<script type="text/javascript">
    let selectedStudentId = null;
    function searchStudent() {
        let query = document.getElementById('studentSearch').value;
        if (query.length < 2) {
            document.getElementById('suggestions').style.display = 'none';
            return;
        }
        fetch(`/payments/search?query=${query}`)
        .then(response => response.json())
        .then(data => {
            let suggestions = document.getElementById('suggestions');
            suggestions.innerHTML = '';
            if (data.length > 0) {
                data.forEach(student => {
                    let item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerText = `${student.firstname} ${student.lastname} (${student.lrn})`;
                    item.onclick = () => selectStudent(student);
                    suggestions.appendChild(item);
                });
                suggestions.style.display = 'block';
            } else {
                suggestions.style.display = 'none';
            }
        });
    }
    function selectStudent(student) {
        document.getElementById('studentSearch').value = `${student.firstname} ${student.lastname}`;
        document.getElementById('suggestions').style.display = 'none';
        selectedStudentId = student.user_id;
        document.getElementById('proceedToPayment').disabled = false;
        loadPaymentHistory(selectedStudentId);
        loadFeeBreakdown(selectedStudentId);
        loadPreviousBalance(selectedStudentId);
    loadPaymentDetails(selectedStudentId); // 
}


// Format date function
function formatDate(isoDate) {
    const date = new Date(isoDate);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}



function loadPaymentHistory(studentId) {
    fetch(`/payments/${studentId}/payments`)
    .then(response => response.json())
    .then(data => {
        let tableBody = document.getElementById('paymentHistory');
        tableBody.innerHTML = '';
        if (data.length > 0) {
            data.forEach(payment => {
                let row = `
                            <tr>
                                <td>${formatDate(payment.payment_date)}</td>
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
function processPayment() {
    if (!selectedStudentId) {
        alert('Please select a student first.');
        return;
    }
    $('#paymentModal').modal('show');
}
function submitPayment() {
    const amount = document.getElementById('paymentAmount').value;
    if (!amount || amount <= 0) {
        alert('Please enter a valid amount.');
        return;
    }
    fetch(`/payments/${selectedStudentId}/walk-in`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            amount_paid: amount,
            payment_method: 'Walk-in'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Toast.fire({
                icon: 'success',
                title: 'Payment successful!'
            });
            $('#paymentAmount').val('');
            $('#paymentModal').modal('hide');
            $('#paymentHistoryTable').DataTable().destroy();
            loadPaymentHistory(selectedStudentId);
            loadFeeBreakdown(selectedStudentId);
            loadPreviousBalance(selectedStudentId);
            loadPaymentDetails(selectedStudentId); 
        } else {
            Toast.fire({
                icon: 'error',
                title: data.message || 'Payment failed.'
            });
        }
    })
    ;
}
</script>
<script type="text/javascript">
    // Add sidebar-closed and sidebar-collapse classes when the site loads
    document.addEventListener('DOMContentLoaded', 
        function () {
            document.body.classList.add('sidebar-closed', 'sidebar-collapse');
            getPendingGcash();
        });
    </script>
    <script type="text/javascript">
        function loadPreviousBalance(studentId) {
            fetch(`/payments/${studentId}/previous-balance`)
            .then(response => response.json())
            .then(data => {
                const balanceContainer = document.getElementById('previousBalance');
                if (data.previous_balance > 0) {
                    balanceContainer.innerHTML = `
                    <div class="alert alert-warning">
                        Outstanding Balance from Previous Years: ₱${parseFloat(data.previous_balance).toFixed(2)}
                    </div>
                    `;
                } else {
                    balanceContainer.innerHTML = '';
                }
            })
            .catch(error => console.error('Error loading previous balance:', error));
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
      
 function getPendingGcash() {
    fetch(`/gcash/allpending`)
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById('pendingGcash');
            tableBody.innerHTML = '';

            if (data.length > 0) {
                data.forEach(pending => {
                    // Format the date like 'March 9, 2025'
                    let date = new Date(pending.created_at);
                    let formattedDate = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    // Handle null profile data
                    let fullName = pending.profile 
                        ? `${pending.profile.firstname} ${pending.profile.lastname}` 
                        : "N/A";
                    
                    let accountName = pending.gcash_information?.account_name || "N/A";

                    let row = `
                        <tr>
                            <td>${formattedDate}</td>
                            <td>₱${parseFloat(pending.amount).toFixed(2)}</td>
                            <td>
                                <a href="#" onclick="showReceiptModal('${pending.reference_number}', '${pending.receipt}', '${fullName}', '${accountName}')">
                                    ${pending.reference_number}
                                </a>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });

                // Initialize DataTable (destroy first if already initialized)
                if ($.fn.DataTable.isDataTable('#pendingGcashTable')) {
                    $('#pendingGcashTable').DataTable().destroy();
                }

                $('#pendingGcashTable').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No pending payments available.</td></tr>';
            }
        })
        .catch(error => console.error('Error loading payment history:', error));
}



   function showReceiptModal(referenceNumber, receipt, sender, receiver) {
    // Set modal content
    document.getElementById('modalReferenceNumber').textContent = referenceNumber;
    document.getElementById('modalSender').textContent = sender;
    document.getElementById('modalReceiver').textContent = receiver;

    const receiptImage = document.getElementById('modalReceiptImage');
    
    if (receipt) {
        receiptImage.src = `/storage/${receipt}`;
        receiptImage.style.display = 'block'; // Show image if available
    } else {
        receiptImage.style.display = 'none'; // Hide if no receipt
    }

    // Show the modal
    $('#receiptModal').modal('show');
}

    </script>

    <script type="text/javascript">
function status(action) {
    const referenceNumber = document.getElementById('modalReferenceNumber').textContent;

    if (!referenceNumber) {
        toastr.error('Reference number is missing!');
        return;
    }

    const confirmation = confirm(`Are you sure you want to mark this payment as ${action}?`);
    if (!confirmation) return;

    fetch(`/gcash/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reference_number: referenceNumber,
            status: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                 Toast.fire({
                icon: data.icon,
                title: data.success
            });
                  $('#pendingGcashTable').DataTable().destroy();
            getPendingGcash();
            $('#receiptModal').modal('hide');
        } else {
             Toast.fire({
                icon: data.icon,
                title: data.error
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
         Toast.fire({
            icon: 'error',
            title: 'Something went wrong!'
        });
    });
}

    </script>
    @endsection