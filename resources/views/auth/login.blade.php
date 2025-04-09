@extends('layouts.master2')

@section('content')
<h3 class="custom-title-epay"> E-PAYMENT</h3>
     <div class="row">
      <div class="col-lg-6">
        <div class="card login-card">

                <div class="card-header text-white" style="font-weight: bold">{{ __('Login') }}</div>
                <div class="card-body">
                  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label for="login" class="form-label text-white">{{ __('Username or Email Address') }}</label>
        <input type="text" id="login" name="login" class="form-control @error('login') is-invalid @enderror" required autofocus>

        @error('login')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label text-white">{{ __('Password') }}</label>
        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">

        @error('password')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
                @if (Route::has('password.request'))
            <a class="btn btn-link text-white" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        @endif
    </div>



    <div class="mb-0">

        <button type="submit" class="btn btn-primary">
            {{ __('Login') }}
        </button>


    </div>
</form>


            </div>
        </div>
      </div>
      <div class="col-lg-6 ">
        <div id="missionVisionCarousel" class="carousel slide " data-ride="carousel">
          <div class="carousel-inner " id="carouselContent">
          </div>
          <a class="carousel-control-prev" href="#missionVisionCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#missionVisionCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
    </div>
@endsection
