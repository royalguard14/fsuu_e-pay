<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <style type="text/css">
    @font-face {
      font-family: 'RobotoBlack';
      src: url('{{ asset('Roboto-Black.ttf') }}') format('truetype');
        font-weight: bold;
        font-style: normal;
      }
      .custom-title {
        font-family: 'RobotoBlack';
        color: white;
        -webkit-text-stroke: 3px black; /* Border around each letter */
        letter-spacing: 1px;
        margin: 0;
        text-align: center;
      }
      .trans{
        background-color: gray; 
        opacity: 0.8;
        display: flex; 
      }
      .mids {
        background-color: red;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
      }
.footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 85%; /* Adjust as needed */
    background: url('{{ asset('footer.png') }}') no-repeat center bottom;
    background-size: 100% 100%; /* Stretches to fit the width and height */
    z-index: 1000; /* Keeps it above other content */
    pointer-events: none; /* Prevents clicks on transparent parts */
}

.centered-container {
    display: flex;
    justify-content: center; /* Centers horizontally */
    align-items: center; /* Centers vertically */
    height: calc(100vh - 30vh); /* 100% of viewport height minus footer height */
    width: 100%;
}
.transpa{
  background-color: transparent;
}


      </style>
    </head>
    <body class="hold-transition layout-top-nav" style="background: url('{{ asset('bg.png') }}') no-repeat center center fixed; background-size: cover;">
      <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light" style="background-color: #003366; border-bottom: 8px solid #FFC107;">
          <div class="container">
            <a href="../../index3.html" class="navbar-brand">
              <img src="logo.png" alt="AdminLTE Logo" class="brand-image img-circle" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
            </a>
            <p class="custom-title" style="color: yellow; font-size: 3rem;">FUAMI E-PAYMENT
            </p>
            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
              <!-- Messages Dropdown Menu -->
            </ul>
          </div>
        </nav>
        <!-- /.navbar -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="background: url('{{ asset('bg.png') }}') no-repeat center center fixed; background-size: cover;">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container">
              <div class="row mb-2">
              </div><!-- /.row -->
            </div><!-- /.container-fluid -->
          </div>
          <!-- /.content-header -->
          <!-- Main content -->
         
            <div class="row m-2 centered-container">
              <div class="col-lg-6 d-flex justify-content-end">
    <div class="card trans" style="width: 50%;">
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Username or Email -->
                <div class="form-group">
                    <label for="login" class="col-form-label text-md-end">{{ __('Username or Email Address') }}</label>
                    <input type="text" id="login" name="login" class="form-control @error('login') is-invalid @enderror" required autofocus>

                    @error('login')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="col-form-label text-md-end">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3 text-center">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <!-- Centered Submit Button -->
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-info w-50">
                        {{ __('Sign in') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

            <!-- /.col-md-6 -->
            <div class="col-lg-6 transpa"  >
              <div class="card transpa" >
                <div class="card-body">
                  <p class="custom-title" style="font-size: 2rem;">FR. URIOS ACADEMY OF</p>
                  <p class="custom-title" style="color: yellow; font-size: 3rem;">MAGALLANES INC.</p>
                </div>
              </div>
            </div>
            <!-- /.col-md-6 -->
          </div>
          <!-- /.row -->
        
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->
      <!-- Main Footer -->
      <footer class="footer">
   
      </footer>
    </div>
<!-- ./wrapper -->
<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js?v=3.2.0"></script>
</body>
</html>