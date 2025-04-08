
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FUAMI E-PAYMENT</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css?v=3.2.0">


  <style type="text/css">

    .logo-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 50%;
}



    .transpa{
  background-color: transparent;

}


.login-card{
  background-color: rgba(128, 128, 128, 0.75);
}

.topnavs{
  background-color: #003366; 
  border-bottom: 8px solid #FFC107;
}



.footline{
  background-color: #003366; 
  border-top: 8px solid #FFC107;
}


.content-wrapper{
  background: url('{{ asset('bg.png') }}') no-repeat center center fixed;
   background-size: cover;
}


    @font-face {
      font-family: 'RobotoBlack';
      src: url('{{ asset('Roboto-Black.ttf') }}') format('truetype');
        font-weight: bold;
        font-style: normal;
      }
.custom-title {
  font-family: 'RobotoBlack';
  color: yellow;
  -webkit-text-stroke: 3px black;
  letter-spacing: 1px;
  margin: 0;
  font-size: 3rem;
}


@media (max-width: 768px) {
  .logo-img {
    width: 60px;
    height: 60px;
  }

  .custom-title {
    font-size: 2.2rem;
    -webkit-text-stroke: 2px black;
  }
}

/* Phones */
@media (max-width: 480px) {
  .logo-img {
    width: 50px;
    height: 50px;
  }

  .custom-title {
    font-size: 1.8rem;
    -webkit-text-stroke: 1.5px black;
  }
}


.footer-logo {
  background-color: #FFC107;
  border-radius: 50%;
  width: 130px;   /* reduced from 150px */
  height: 130px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  bottom: 10px;
  right: 30px;
  z-index: 10;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

.footer-logo img {
  width: 115px;   /* reduced proportionally */
  height: 115px;
  object-fit: cover;
  border-radius: 50%;
}

/* Smaller screens (tablets) */
@media (max-width: 768px) {
  .footer-logo {
    width: 120px;
    height: 120px;
    right: 20px;
  }

  .footer-logo img {
    width: 100px;
    height: 100px;
  }
}

/* Phones */
@media (max-width: 480px) {
  .footer-logo {
    width: 90px;
    height: 90px;
    right: 15px;
  }

  .footer-logo img {
    width: 70px;
    height: 70px;
  }
}


  </style>

</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white topnavs">


<div class="container">
  <a href="../../index3.html" class="navbar-brand d-flex align-items-center">
    <img src="logo.png" alt="AdminLTE Logo" class="logo-img">
    <span class="custom-title ms-3">FUAMI E-PAYMENT</span>
  </a>
</div>


  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">

  
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
   @yield('content')
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->

  <footer class="main-footer footline">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
       <div class="footer-logo">
  <img src="footerlogo.png" alt="Logo">
</div>

    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2025 <a href="https://www.facebook.com/profile.php?id=61572728822378">MGX TECH</a>.</strong> All rights reserved.
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




<script>
  $(document).ready(function() {
    $.ajax({
      url: '/about',
      type: 'GET',
      success: function(data) {
        let carouselContent = '';

        if (data.mission) {
          const missionFormatted = data.mission.replace(/\n/g, '<br>');
          carouselContent += `
            <div class="carousel-item active">
              <div class="card bg-light">
                <div class="card-body">
                  <h5 class="card-title">Our Mission</h5>
                  <p class="card-text">${missionFormatted}</p>
                </div>
              </div>
            </div>
          `;
        }

        if (data.vision) {
          const visionFormatted = data.vision.replace(/\n/g, '<br>');
          carouselContent += `
            <div class="carousel-item ${data.mission ? '' : 'active'}">
              <div class="card bg-light">
                <div class="card-body">
                  <h5 class="card-title">Our Vision</h5>
                  <p class="card-text">${visionFormatted}</p>
                </div>
              </div>
            </div>
          `;
        }

        $('#carouselContent').html(carouselContent);
      },
      error: function(xhr) {
        console.error('Error fetching mission and vision:', xhr);
      }
    });
  });
</script>



</body>
</html>
