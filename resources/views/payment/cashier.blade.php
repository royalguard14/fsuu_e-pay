@extends('layouts.master')
@section('header')
Payment Management
@endsection
@section('style')
<style type="text/css">
.hover-receipt-image {
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    border-radius: 4px;
}


.hides {
    display: none;
}
</style>



<style type="text/css">

</style>


@endsection

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

                    <button id="printButton" class="btn btn-primary col-lg-12 mt-2" style="display:none;" disabled >Print</button>
                </div>
            </div>

<div class="card card-tabs">
  <div class="card-header p-0 pt-1">
    <ul class="nav nav-tabs" id="gcash-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="pending-tab" data-toggle="pill" href="#pending" role="tab" aria-controls="pending" aria-selected="true">GCash Notification</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="approved-tab" data-toggle="pill" href="#approved" role="tab" aria-controls="approved" aria-selected="false">Approved</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="rejected-tab" data-toggle="pill" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">Rejected</a>
      </li>
    </ul>
  </div>
  <div class="card-body">
    <div class="tab-content" id="gcash-tabs-content">
      <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
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
      <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
        <table class="table table-head-fixed text-nowrap table-striped" id="approvedGcashTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Reference No.</th>
            </tr>
          </thead>
          <tbody id="approvedGcash"></tbody>
        </table>
      </div>
      <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
        <table class="table table-head-fixed text-nowrap table-striped" id="rejectedGcashTable">
          <thead>
            <tr>
              <th>Reference No.</th>
              <th>Account Name</th>
              <th>Reason</th>
            </tr>
          </thead>
          <tbody id="rejectedGcash"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>







<!-- This is the printable section -->
<div class="hides" id="studentSummary">
    <p><strong>Name of Student:</strong> <span id="printStudentName"></span></p>
    <p><strong>Total Fees:</strong> ₱<span id="printTotalFees"></span></p>
    <p><strong>Total Amount Paid:</strong> <span id="printAmountPaid"></span></p>
    <p><strong>Total Balance:</strong> ₱<span id="printBalance"></span></p>
</div>

<table border="1" cellspacing="0" cellpadding="5" width="100%" class="hides">
    <thead>
        <tr>
            <th>#</th>
            <th>Reference No.</th>
            <th>Method</th>
            <th>Date</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody id="paymentHistoryTableBody"></tbody>
</table>

<!-- Submitted By section -->
<div class="row hides" id="submittedBySection">
    <div class="col-6" style="text-align:left">
        <p>Submitted By:</p><br>
        <p style="text-decoration: underline; margin-left: 30px;">{{ $cashierName }}</p>
        <p style="margin-left: 30px;">School Cashier</p>
    </div>
</div>

<script type="text/javascript">
 document.getElementById('printButton').addEventListener('click', function () {2
    let paymentRows = '';
    paymentData.paymentHistory.forEach((payment, index) => {
        const paymentDate = new Date(payment.payment_date).toLocaleDateString();
        paymentRows += `
            <tr>
                <td>${index + 1}</td>
                <td>${payment.reference_number}</td>
                <td>${payment.payment_method}</td>
                <td>${paymentDate}</td>
                <td>₱${parseFloat(payment.amount_paid).toFixed(2)}</td>
            </tr>
        `;
    });

    const summaryHtml = `
        <div>
           <p><span style="font-weight: bold;">Full Name:</span> <span style="text-decoration: underline;">${paymentData.fullName}</span></p>


        </div>
        <h3>Payment History</h3>
        <table border="1" cellspacing="0" cellpadding="5" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reference No.</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                ${paymentRows}
            </tbody>
        </table>
        <p><span class="bold">Total Paid:</span> ₱${paymentData.totalPaid}</p>
            <p><span class="bold">Total Fees:</span> ₱${paymentData.totalFees}</p>
            <p><span class="bold">Total Balance:</span> ₱${paymentData.totalBalance}</p>
    `;

    const extraContent = document.getElementById('submittedBySection').innerHTML;

    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <html>
            <head>
                <style>



@media print {

    #totalRow {
        display: table-row !important;
        border-bottom: 0;
    }

    #totalAmount {
        font-weight: bold;
    }


    .hides {
        display: block !important; 
             font-weight: bold;
            font-size: 18px;
    }
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }

    .card-header {
        display: none;  /* Hide card header during printing */
    }

    #paymentTable {
        width: 100%;
        border-collapse: collapse;
    }

    #paymentTable th, #paymentTable td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    #paymentTable th {
        background-color: #f2f2f2;
    }

    /* Hide the footer during printing */
    footer {
        display: none; /* Hide footer */
    }

    /* Hide the browser's address bar or any URL displayed during printing */
    header, nav, footer, .navbar, .breadcrumb {
        display: none;
    }

    /* Page setup for A4 paper */
    @page {
        size: A4;  /* Set paper size to A4 */
        margin: 20mm;  /* Set margins to 20mm */
        @top-center {
            content: "FATHER URIOS ACADEMY OF MAGALLANES, INC.";  /* Custom header */
            font-weight: bold;
            font-size: 18px;
        }
        @bottom-center {
            content: "";  /* Remove footer content */
        }
    }

    /* Optional: Adjust page break to avoid cutting off the table */
    .table-container {
        page-break-after: always;
    }

    /* Add space between sections when printing */
    #submittedBySection {
        margin-top: 20px;  /* Add space before Submitted By */
    }

        #submittedBySection p {
        margin: 0;
        padding: 0;
    }

        /* Insert line breaks before and after Submitted By */
    #submittedBySection:before {
        content: "\A"; /* Line break before Submitted By */
        white-space: pre;
    }

    #submittedBySection:after {
        content: "\A"; /* Line break after Submitted By */
        white-space: pre;
    }







}




















                </style>
            </head>
            <body>
                <h2 style="text-align: center;">Student Payment Summary</h2>
                ${summaryHtml}
                <br/>
                ${extraContent}
                <script>
                    window.print();
                    window.onafterprint = function () {
                        window.close();
                    };
                <\/script>
            </body>
        </html>
    `);

    printWindow.document.close();
});

</script>





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
    <h5>Total fee: <span id="totalfee">₱0.00</span></h5>
    <h5>Total Balance: <span id="totalBalance">₱0.00</span></h5>
    
</div>
<table class="table table-striped" id="monthlyPaymentsTable">
    <thead>
        <tr>
            <th>Month</th>
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
    <div class="modal-dialog modal-lg">
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
    <div id="rejectionReasonContainer" class="w-100 mb-2" style="display: none;">
        <label for="rejectionReason" class="text-white">Reason for Rejection:</label>
        <select id="rejectionReason" class="form-control">
            <option value="" selected disabled>Select a reason</option>
            <option value="Invalid reference number">Invalid reference number</option>
            <option value="Receipt is unclear or unreadable">Receipt is unclear or unreadable</option>
            <option value="Amount does not match">Amount does not match</option>
            <option value="Duplicate transaction">Duplicate transaction</option>
            <option value="Incomplete details">Incomplete details</option>
        </select>
    </div>

    <button type="button" class="btn btn-outline-light" onclick="status('rejected')">Reject</button>
    <button type="button" class="btn btn-outline-light" onclick="status('approved')">Approve</button>
</div>

        </div>
    </div>
</div>

<script type="text/javascript">
    function showReasonField() {
    document.getElementById('rejectionReasonWrapper').style.display = 'block';
}
</script>
<script>
    let paymentData = null;
</script>

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
    selectedStudentId = student.user_id;



    document.getElementById('studentSearch').value = `${student.firstname} ${student.lastname}`;
    document.getElementById('suggestions').style.display = 'none';
    document.getElementById('proceedToPayment').disabled = false;

    // Load data
    loadPaymentHistory(selectedStudentId);
    loadFeeBreakdown(selectedStudentId);
    loadPreviousBalance(selectedStudentId);
    loadPaymentDetails(selectedStudentId);
    printDetails(selectedStudentId);


}


function printDetails(studentId) {
    fetch(`/payments/${studentId}/printable-transactions`) 
        .then(response => response.json())
        .then(data => {
            // Check for error in response
            if (data.error) {
                console.error(data.error);  // Log the error
                // Hide the print button if no active enrollment found
                const printButton = document.getElementById('printButton');
                printButton.style.display = 'none';  // Hide the button
                printButton.disabled = true;  // Disable the button
                return; // Exit the function early
            }

            paymentData = data;

            // Set the student summary
            document.getElementById('printStudentName').textContent = data.fullName;
            document.getElementById('printTotalFees').textContent = data.totalFees;
            document.getElementById('printAmountPaid').textContent = data.totalPaid;
            document.getElementById('printBalance').textContent = data.totalBalance;

            // Generate table in UI
            const tableBody = document.getElementById('paymentHistoryTableBody');
            tableBody.innerHTML = '';

            data.paymentHistory.forEach((payment, index) => {
                const paymentDate = new Date(payment.payment_date).toLocaleDateString();
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${payment.reference_number}</td>
                    <td>${payment.payment_method}</td>
                    <td>${paymentDate}</td>
                    <td>₱${parseFloat(payment.amount_paid).toFixed(2)}</td>
                `;
                tableBody.appendChild(row);
            });

            // ✅ Show the Print button after data is loaded
            const printButton = document.getElementById('printButton');
            printButton.textContent = `Print (${data.fullName}) Payment Details`;
            printButton.style.display = 'inline-block';
            printButton.disabled = false;
        })
        .catch(error => console.error('Error fetching transactions:', error));
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
                        <td><strong>₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>

                    </tr>
            `;


// Update total fee in the <span> element
document.getElementById('totalfee').textContent = `₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;



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
            getAllGcashTransactions();

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
                // Check if there's an error message indicating no enrollment
                if (data.message && data.message === "No enrollment found") {
                    document.getElementById('totalPaid').innerText = `₱00.00`;
                    document.getElementById('totalBalance').innerText = `₱00.00`;
                    //document.getElementById('suggestedAmount').innerText = `₱00.00`;
                    document.getElementById('totalfee').innerText = `₱00.00`;
                    const tableBody = document.querySelector('#monthlyPaymentsTable tbody');
                    tableBody.innerHTML = ''; // Clear existing rows
                    return; // Exit the function as no data is available
                }

                // Set the payment details if data is valid
                document.getElementById('totalPaid').innerText = `₱${data.totalPaid}`;
                document.getElementById('totalBalance').innerText = `₱${data.totalBalance}`;
                //document.getElementById('suggestedAmount').innerText = `₱${data.suggestedAmount}`;

                // Generate the monthly payments table
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
function getAllGcashTransactions() {
    fetch(`/gcash/allpending`)
        .then(response => response.json())
        .then(data => {
            // Clear table bodies first
            document.getElementById('pendingGcash').innerHTML = '';
            document.getElementById('approvedGcash').innerHTML = '';
            document.getElementById('rejectedGcash').innerHTML = '';

            // Helper function to format date
            function formatDate(dateString) {
                let date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            // ========== Pending ==========
            if (data.pending && data.pending.length > 0) {
                data.pending.forEach(pending => {
                    let fullName = pending.profile 
                        ? `${pending.profile.firstname} ${pending.profile.lastname}` 
                        : "N/A";
                    
                    let accountName = pending.gcash_information?.account_name || "N/A";

                    let row = `
                        <tr>
                            <td>${formatDate(pending.created_at)}</td>
                            <td>₱${parseFloat(pending.amount).toFixed(2)}</td>
                            <td>
                                <a href="#" onclick="showReceiptModal('${pending.reference_number}', '${pending.receipt}', '${fullName}', '${accountName}', 'pending')">${pending.reference_number}</a>
                            </td>
                        </tr>
                    `;
                    document.getElementById('pendingGcash').innerHTML += row;
                });
            } else {
                document.getElementById('pendingGcash').innerHTML = '<tr><td colspan="3" class="text-center">No pending payments available.</td></tr>';
            }

            // ========== Approved ==========
            if (data.approved && data.approved.length > 0) {
                data.approved.forEach(approved => {
                    let fullName = approved.profile 
                        ? `${approved.profile.firstname} ${approved.profile.lastname}` 
                        : "N/A";
                    
                    let accountName = approved.gcash_information?.account_name || "N/A";

                    let row = `
                        <tr>
                            <td>${formatDate(approved.created_at)}</td>
                            <td>₱${parseFloat(approved.amount).toFixed(2)}</td>
                            <td style="position: relative;">
                                <a href="#" 
                                    onclick="showReceiptModal('${approved.reference_number}', '${approved.receipt}', '${fullName}', '${accountName}', 'approved')"
                                    onmouseover="showHoverImage(this, '${approved.receipt}')" 
                                    onmouseout="hideHoverImage(this)">
                                    ${approved.reference_number}
                                </a>
                                <div class="hover-receipt-image" style="display: none; position: absolute; top: 20px; left: 100%; z-index: 999; background: white; border: 1px solid #ccc; padding: 5px;">
                                    <img src="/storage/${approved.receipt}" alt="Receipt" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </td>
                        </tr>
                    `;
                    document.getElementById('approvedGcash').innerHTML += row;
                });
            } else {
                document.getElementById('approvedGcash').innerHTML = '<tr><td colspan="3" class="text-center">No approved payments available.</td></tr>';
            }

            // ========== Rejected ==========
            if (data.rejected && data.rejected.length > 0) {
                data.rejected.forEach(rejected => {
                    let fullName = rejected.profile 
                        ? `${rejected.profile.firstname} ${rejected.profile.lastname}` 
                        : "N/A";

                    let accountName = rejected.gcash_information?.account_name || "N/A";
                    let reason = rejected.reason || 'No reason provided';

                    let row = `
                        <tr>
                            <td>${rejected.reference_number}</td>
                            <td>${accountName}</td>
                            <td>${reason}</td>
                        </tr>
                    `;
                    document.getElementById('rejectedGcash').innerHTML += row;
                });
            } else {
                document.getElementById('rejectedGcash').innerHTML = '<tr><td colspan="3" class="text-center">No rejected payments available.</td></tr>';
            }

            // Ensure tables have content before initializing DataTables
            setTimeout(function () {
                // Only initialize DataTables after the content is fully loaded
                ['pendingGcashTable', 'approvedGcashTable', 'rejectedGcashTable'].forEach(id => {
                    if ($.fn.DataTable.isDataTable(`#${id}`)) {
                        $(`#${id}`).DataTable().destroy();
                    }

                    $(`#${id}`).DataTable({
                        "paging": true,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": false,
                        "info": false,
                        "autoWidth": false,
                        "responsive": true,
                    });
                });
            }, 100); // Delay initialization to allow DOM updates
        })
        .catch(error => console.error('Error loading GCash transactions:', error));
}




function showReceiptModal(referenceNumber, receipt, sender, receiver, status = 'pending') {
    document.getElementById('modalReferenceNumber').textContent = referenceNumber;
    document.getElementById('modalSender').textContent = sender;
    document.getElementById('modalReceiver').textContent = receiver;

    const receiptImage = document.getElementById('modalReceiptImage');
    const noReceiptMessage = document.getElementById('noReceiptMessage');
    const modalFooter = document.querySelector('#receiptModal .modal-footer');

    if (receipt) {
        receiptImage.src = `/storage/${receipt}`;
        receiptImage.style.display = 'block';
        noReceiptMessage.style.display = 'none';
    } else {
        receiptImage.style.display = 'none';
        noReceiptMessage.style.display = 'block';
    }

    // Show or hide footer based on status
    if (status === 'pending') {
        modalFooter.style.display = 'flex'; // or 'block'
    } else {
        modalFooter.style.display = 'none';
    }

    $('#receiptModal').modal('show');
}




</script>

<script type="text/javascript">

    function status(action) {
        const referenceNumber = document.getElementById('modalReferenceNumber').textContent;
        const reasonSelect = document.getElementById('rejectionReason');

        if (!referenceNumber) {
            toastr.error('Reference number is missing!');
            return;
        }

        if (action === 'rejected') {
            // Show the dropdown if not shown yet
            document.getElementById('rejectionReasonContainer').style.display = 'block';

            if (!reasonSelect.value) {
                toastr.warning('Please select a reason for rejection.');
                return;
            }
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
                status: action,
                reason: action === 'rejected' ? reasonSelect.value : null
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
                getAllGcashTransactions();
                $('#receiptModal').modal('hide');
                reasonSelect.value = ''; // Reset reason
                document.getElementById('rejectionReasonContainer').style.display = 'none'; // Hide again
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


<script>
    function showHoverImage(element, imageName) {
        const div = element.parentElement.querySelector('.hover-receipt-image');
        div.style.display = 'block';
    }

    function hideHoverImage(element) {
        const div = element.parentElement.querySelector('.hover-receipt-image');
        div.style.display = 'none';
    }
</script>

@endsection