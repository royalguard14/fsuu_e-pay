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
<div class="row">
    <!-- Left Side: Student Search and Payment History -->
    <section class="col-lg-7 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment History</h3>
            </div>
            <div class="card-body">
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
    <section class="col-lg-5 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Fee Breakdown</h3>
            </div>
            <div class="card-body">
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
        </div>
    </section>
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
                                <td>${payment.payment_date}</td>
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
                data.forEach(fee => {
                    let row = `
                            <tr>
                                <td>${fee.fee_type}</td>
                                <td>₱${parseFloat(fee.amount).toFixed(2)}</td>
                            </tr>
                    `;
                    tableBody.innerHTML += row;
                });
                $('#feeBreakdownTable').DataTable().destroy();
                $('#feeBreakdownTable').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
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
document.addEventListener('DOMContentLoaded', function () {
    document.body.classList.add('sidebar-closed', 'sidebar-collapse');
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
@endsection