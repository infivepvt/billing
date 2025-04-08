<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Infive Print</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">

  <style>
        /* Hide the navbar by default */
        .main-navbar {
            display: none;
        }
        .navbar-bg {
            display: none;
        }

        /* Show the navbar on screens smaller than 992px (bootstrap's lg breakpoint) */
        @media (max-width: 991.98px) {
            .main-navbar {
                display: flex;
            }

            .navbar-bg {
                display: flex;
            }
            /* Ensure the main content is pushed down when the navbar is visible */
            
            .main-content {
                padding-top: 140px; /* Adjust according to your navbar height */
            }
        }

        /* Ensure the mobile menu is positioned correctly */
        .navbar-bg,
        .main-navbar {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        
    </style>


</head>

<body>
  
  <div id="app" >
    <div class="main-wrapper main-wrapper-1" >
      <div class="navbar-bg" style="background-color: #1BA664;color: white;"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><img src="assets/img/infive_logo.jpg"></li>
          </ul>
      </nav>
      <?php include('left_menu.php'); ?>

      <!-- Main Content -->
      <div class="main-content" style="background-color:#F7F9F9;">
        
        <section class="section" >
          <div >
            <h1>Dashboard Work</h1>
          </div>

          <div class="section-body" >asdf
          </div>
        </section>
      </div>
      <?php include('footer.php'); ?>
    </div>
  </div>


  <!-- General JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>