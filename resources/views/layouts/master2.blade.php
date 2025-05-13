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
   /* Logo Image Styling */
   .logo-img {
    width: 5vw;  /* Logo width based on viewport width */
    height: auto;  /* Keep the aspect ratio */
    object-fit: cover; /* Ensures logo is not distorted */
    border-radius: 50%; /* Makes the logo round */
  }
/* Transparent Background */
.transpa {
  background-color: transparent;
}
/* Login Card Styling */
.login-card {
  background-color: rgba(128, 128, 128, 0.75);
}
/* Navbar and Footer Background */
.topnavs {
  background-color: #003366; 
  border-bottom: 8px solid #FFC107;
}
.footline {
  background-color: #003366; 
  border-top: 8px solid #FFC107;
}
/* Background for content wrapper */
.content-wrapper {
  background: url('{{ asset('bg.png') }}') no-repeat center center fixed;
    background-size: cover;
  }
/* Font Face for Roboto Black */
@font-face {
  font-family: 'RobotoBlack';
  src: url('{{ asset('Roboto-Black.ttf') }}') format('truetype');
    font-weight: bold;
    font-style: normal;
  }
/* Title Styling */
.custom-title {
  font-family: 'RobotoBlack', sans-serif;
  color: yellow;
  -webkit-text-stroke: 2px black;
  margin: 0;
  font-size: 3vw;  /* Base font size, responsive with viewport width */
  letter-spacing: 1px;
  white-space: nowrap;  /* Prevent text wrapping */
}


    .custom-title-epay {
  font-family: 'RobotoBlack', sans-serif;
  color: yellow;
  -webkit-text-stroke: 2px black;
  margin: 0;
  font-size: 5vw;  /* Base font size, responsive with viewport width */
  letter-spacing: 1px;
  white-space: nowrap;  /* Prevent text wrapping */
  }
/* Adjust logo size and font size on smaller screens */
@media (max-width: 768px) {
  .logo-img {
    width: 7vw; /* Increase logo size on tablet devices */
  }
  .custom-title {
    font-size: 3vw; /* Adjust font size for tablets */
  }


  .custom-title-epay {
  font-family: 'RobotoBlack', sans-serif;
  color: yellow;
  -webkit-text-stroke: 1px black;
  margin: 0;
  font-size: 3vw;  /* Base font size, responsive with viewport width */
  letter-spacing: 1px;
  white-space: nowrap;  /* Prevent text wrapping */
  }


}
/* Adjust logo size and font size on mobile screens */
@media (max-width: 480px) {
  .logo-img {
    width: 10vw; /* Larger logo on mobile devices */
  }
  .custom-title {
    font-size: 3vw; /* Larger text size on mobile */
     -webkit-text-stroke: 1px black;
  }

    .custom-title-epay {
  font-family: 'RobotoBlack', sans-serif;
  color: yellow;
  -webkit-text-stroke: 1px black;
  margin: 0;
  font-size: 5vw;  /* Base font size, responsive with viewport width */
  letter-spacing: 1px;
  white-space: nowrap;  /* Prevent text wrapping */
  }

}
/* Footer Logo Styling */
.footer-logo {
  background-color: #FFC107;
  border-radius: 50%;
  width: 130px; /* Reduced from 150px */
  height: 130px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  bottom: 10px;
  right: 30px;
  z-index: 10;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}
.footer-logo img {
  width: 115px; /* Reduced proportionally */
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
.tops {
  display: flex;
  align-items: center; /* Vertically aligns logo and text */
  justify-content: flex-start; /* Aligns to the left */
  padding: 10px;
  text-align: left;
}
</style>
</head>
<body class="hold-transition layout-top-nav">
  <div class="wrapper">


     <div class="main-header topnavs tops">

  <img src="footerlogo.png" alt="AdminLTE Logo" class="logo-img">
  <span class="custom-title">FATHER URIOS ACADEMY OF MAGALLANES, INC.</span>
</div>
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
              <div class="card login-card">
                <div class="card-body">
                  <h5 class="card-title text-white font-weight-bold">Our Mission</h5>
                  <p class="card-text text-white">${missionFormatted}</p>
                </div>
              </div>
            </div>
          `;
        }
        if (data.vision) {
          const visionFormatted = data.vision.replace(/\n/g, '<br>');
          carouselContent += `
            <div class="carousel-item ${data.mission ? '' : 'active'}">
              <div class="card login-card">
                <div class="card-body">
                  <h5 class="card-title text-white font-weight-bold">Our Vision</h5>
                  <p class="card-text text-white">${visionFormatted}</p>
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