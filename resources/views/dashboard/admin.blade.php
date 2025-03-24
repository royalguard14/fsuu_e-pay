@extends('layouts.master')
@section('header')
Administration Dashboard
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
<div class="card">
    <div class="card-header text-center"><h3> SY: {{$currentAcademicYear->start}} - {{$currentAcademicYear->end}}</h3></div>
</div>
<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wallet"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Expected Collection</span>
                <span class="info-box-number">₱{{ number_format($totalExpected, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-hand-holding-usd"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Money Collected</span>
                <span class="info-box-number">₱{{ number_format($totalCollected, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Number of Students</span>
                <span class="info-box-number">{{ number_format($totalStudents) }}</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
  <div class="col-md-6">
      <div class="card">
        <div class="card-header text-center">
            <h3> Payment Graph</h3>
        </div>
        <div class="card-body">
         <canvas id="collectionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
         <script>
            const ctx = document.getElementById('collectionChart').getContext('2d');
   // Function to generate random RGBA color
            function getRandomColor(opacity = 0.5) {
                const r = Math.floor(Math.random() * 256);
                const g = Math.floor(Math.random() * 256);
                const b = Math.floor(Math.random() * 256);
                return `rgba(${r}, ${g}, ${b}, ${opacity})`;
            }
            let tecc = getRandomColor(opacity = 0.5);
            let tmcc = getRandomColor(opacity = 0.5)
            const collectionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($gradeLabels),
                    datasets: [
                        {
                            label: 'Total Expected Collection',
                            data: @json($expectedCollections),
                            backgroundColor: tecc,
                            borderColor: tecc,
                            borderWidth: 1
                        },
                        {
                            label: 'Total Money Collected',
                            data: @json($collectedMoney),
                            backgroundColor: tmcc,
                            borderColor: tmcc,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
</div>
</div>
<div class="col-md-6">
    <div class="card">
        <div class="card-header text-center"><h3> Daily Payment Graph</h3></div>
        <div class="card-body">
            <canvas id="monthlyCollectionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            <script>
                const lineCtx = document.getElementById('monthlyCollectionChart').getContext('2d');
                const monthlyCollectionChart = new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: @json($days),
                        datasets: [
                            {
                                label: 'Total Money Collected',
                                data: @json($dailyTotals),
                                backgroundColor: getRandomColor(opacity = 0.70),
                                borderColor: getRandomColor(opacity = 0.5),
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>
</div>
@endsection