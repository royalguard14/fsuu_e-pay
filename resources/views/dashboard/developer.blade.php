@extends('layouts.master')
@section('header')
Developer Dashboard
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
  <!-- Left col: Create Role -->
  <section class="col-lg-6 connectedSortable">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Visitor Statistics (Last 7 Days)</h3>
        </div>
        <div class="card-body">
            <canvas id="visitorChart"></canvas>
        </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Latest Members</h3>
        <div class="card-tools">
          <span class="badge badge-danger">{{ $latestUsers->filter(function($user) {
    return $user->profile != null;
})->count() }} New Members</span>
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
        </button>
        <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<!-- /.card-header -->
<div class="card-body p-0">
    <ul class="users-list clearfix">
  @if($latestUsers->isEmpty() || $latestUsers->contains(function($user) {
    return is_null($user->profile);
}))





        <li class="item text-center">
            <p>No User found.</p>
        </li>
        @else
        @foreach($latestUsers as $user)
        @if($user->profile)  {{-- Check if profile exists --}}
        <li>
            <img 
            src="{{ $user->profile->profile_picture && file_exists(storage_path('app/public/' . $user->profile->profile_picture)) 
            ? asset('storage/' . $user->profile->profile_picture) 
            : asset('dist/img/user1-128x128.jpg') }}" 
            alt="User Image">
            <a class="users-list-name" href="#">{{ $user->profile->lastname }}, {{ $user->profile->firstname }}</a>
            <span class="users-list-date">{{ $user->created_at->diffForHumans() }}</span>
        </li>
        @endif
        @endforeach
        @endif
    </ul>
    <!-- /.users-list -->
</div>
</div>
<!--/.card -->
</section>
<!-- Left col: Create Role -->
<section class="col-lg-6 connectedSortable">
 <!-- PRODUCT LIST -->
 <div class="card">
  <div class="card-header">
    <h3 class="card-title">Recently Activities</h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
    </button>
    <button type="button" class="btn btn-tool" data-card-widget="remove">
        <i class="fas fa-times"></i>
    </button>
</div>
</div>
<!-- /.card-header -->
<div class="card-body p-0">
    <ul class="products-list product-list-in-card pl-2 pr-2">
        @if($activityLogs->isEmpty())
        <li class="item text-center">
            <p>No activity logs found.</p>
        </li>
        @else
        @foreach($activityLogs as $log)
        <li class="item">
            <div class="product-img">
              <img 
              src="{{ $log->causer->profile && $log->causer->profile->profile_picture && file_exists(storage_path('app/public/' . $log->causer->profile->profile_picture)) 
              ? asset('storage/' . $log->causer->profile->profile_picture) 
              : asset('dist/img/default-150x150.png') }}"
              >
          </div>
          <div class="product-info">
              <a href="javascript:void(0)" class="product-title"> {{ $log->causer->profile ? $log->causer->profile->firstname . ' ' . $log->causer->profile->lastname : 'System' }}
                <span class="badge badge-warning float-right">{{ $log->created_at->diffForHumans() }}</span></a>
                <span class="product-description">
                    {{ $log->description }}
                </span>
            </div>
        </li>
        @endforeach
        @endif
    </ul>
</div>
</div>
<!-- /.card -->
</section>
</div>
@endsection
@section('scripts')
<script>
    const ctx = document.getElementById('visitorChart').getContext('2d');
    const visitorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dates) !!},
            datasets: [{
                label: 'Visitors',
                data: {!! json_encode($counts) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
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
@endsection