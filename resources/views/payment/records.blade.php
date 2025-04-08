@extends('layouts.master')

@section('header')
Payment Records
@endsection

@section('style')
<style type="text/css">
@media print {

    #totalRow {
        display: table-row !important;
        border-bottom: 0;
    }

    #totalAmount {
        font-weight: bold;
    }


    .hides {
        display: block !important;  /* Force display block during printing */
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
            content: "Payment Records";  /* Custom header */
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



        .card, .col-4, .col-8, footer, header, .navbar, .breadcrumb {
        border: none !important;  /* Ensure these elements do not have borders */
    }



}

/* Hide elements by default */
#totalRow {
    display: none;
}

.hides {
    display: none;
}
</style>

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

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Payment Records</h3>
<div class="card-tools">
        <!-- Dropdown for selecting month-year -->
        <select id="monthYearDropdown" class="form-control" style="width: 200px; display: inline-block;">
            <option value="">Select Month-Year</option>
            @foreach($monthYears as $monthYear)
                <option value="{{ $monthYear }}">{{ \Carbon\Carbon::parse($monthYear.'-01')->format('F Y') }}</option>
            @endforeach
        </select>

        <!-- Print Button -->
        <button id="printButton" class="btn btn-primary float-right">Print</button>
    </div>
    </div>

    <div class="card-body">
        <!-- "Payment Summary" text to appear before the table -->
<div class="row" id="subheding">
        <div class="col-6 hides"  id="summaryLabel">
            Payment Summary
        </div>


        <div class="col-6 hides" style=" text-align: right;" id="monthReport">
            ####
        </div>
</div>

        <table class="table table-bordered table-hover" id="paymentTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>GCash / Walk-In</th>
                </tr>
            </thead>
            <tbody id="paymentTableBody">
                @foreach($payments as $payment)
                    <tr class="payment-row" data-month-year="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m') }}">
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}</td>
                        <td>{{ $payment->enrollmentHistory->user->profile->firstname ?? 'N/A' }} {{ $payment->enrollmentHistory->user->profile->lastname ?? '' }}</td>
                        <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                        <td>{{ $payment->payment_method }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        <div class="row">
            <div class="col-6">
                
            </div>
 <div class="col-6 hides" style="text-align:right;">
    <p><strong>Total:</strong> <span id="totalAmount">₱0.00</span></p>
</div>

        </div>





<div class="row hides" id="submittedBySection">
    
    <div class="col-6" style="text-align:left">
        <p style="margin: 0; padding: 0;">Submitted By:</p><br>
        <p style="text-decoration: underline; margin: 0; padding: 0; margin-left: 30px;">{{$cashierName}}</p>
        <p style="margin: 0; padding: 0; margin-left: 30px;">School Cashier</p>
    </div>
    <div class="col-6"></div>
</div>



</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthYearDropdown = document.getElementById('monthYearDropdown');
    const paymentTableBody = document.getElementById('paymentTableBody');
    const totalAmountCell = document.getElementById('totalAmount');
    const monthReport = document.getElementById('monthReport');
    const summaryLabel = document.getElementById('summaryLabel');

    function calculateTotal(selectedMonthYear) {
        let totalAmount = 0;

        const rows = paymentTableBody.querySelectorAll('.payment-row');
        rows.forEach(row => {
            const rowMonthYear = row.getAttribute('data-month-year');
            if (selectedMonthYear === '' || rowMonthYear === selectedMonthYear) {
                const amountCell = row.cells[3];
                const amount = parseFloat(amountCell.textContent.replace('₱', '').replace(/,/g, ''));
                if (!isNaN(amount)) {
                    totalAmount += amount;
                }
            }
        });

        totalAmountCell.textContent = '₱' + totalAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    monthYearDropdown.addEventListener('change', function() {
        const selectedMonthYear = this.value;

        const rows = paymentTableBody.querySelectorAll('.payment-row');
        rows.forEach(row => {
            const rowMonthYear = row.getAttribute('data-month-year');
            row.style.display = (selectedMonthYear === '' || rowMonthYear === selectedMonthYear) ? '' : 'none';
        });

        if (selectedMonthYear) {
            const formattedMonthYear = new Date(selectedMonthYear + '-01').toLocaleString('default', { month: 'long', year: 'numeric' });
            monthReport.textContent = formattedMonthYear;
            summaryLabel.classList.add('hides'); // You may use .classList.remove('hides') if you want it shown
            document.getElementById('printButton').style.display = 'inline-block';
        } else {
            monthReport.textContent = '####';
            summaryLabel.classList.add('hides');
            document.getElementById('printButton').style.display = 'none';
        }

        calculateTotal(selectedMonthYear);
    });

    // Set default view
    if (monthYearDropdown.value) {
        monthYearDropdown.dispatchEvent(new Event('change'));
    } else {
        document.getElementById('printButton').style.display = 'none';
        calculateTotal('');
    }

    // Print button
    document.getElementById('printButton').addEventListener('click', function() {
        window.print();
    });
});
</script>


<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    const paymentTableBody = document.getElementById('paymentTableBody');
    const totalAmountElement = document.getElementById('totalAmount');

    let totalAmount = 0;

    const rows = paymentTableBody.querySelectorAll('tr');
    rows.forEach(row => {
        const amountCell = row.cells[3];
        const amountText = amountCell.textContent.replace('₱', '').replace(/,/g, '');
        const amount = parseFloat(amountText);
        if (!isNaN(amount)) {
            totalAmount += amount;
        }
    });

    totalAmountElement.textContent = '₱' + totalAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
});
</script>


@endsection

@push('scripts')
<!-- You can place any additional scripts here -->
@endpush
