<?php
session_start();
include('db.php'); 
$_SESSION['logged_id'] = 0;

if(isset($_POST['email']) && isset($_POST['password']))
{
  $email = $_POST['email'];
  $password = $_POST['password'];

  if($email == 'infivellc@gmail.com'  && $password == 'bm@123' )
  {
    $_SESSION['logged_id'] = 1;
    
    header('Location: ./dashboard');
   
  }
  else
  {
    $_SESSION['logged_id'] = 0;
    print '<h3>Email / Password is not correct</h3>';
    
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Infive Print</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">

</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <img src="assets/img/infive_logo.png" alt="logo" width="100" class="shadow-light rounded-circle">
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Login</h4></div>

              <div class="card-body">
                <form method="POST" action="./" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                    <div class="invalid-feedback">
                      Please fill in your email
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">Password</label>
                      
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                    <div class="invalid-feedback">
                      please fill in your password
                    </div>
                  </div>


                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
                
                
              </div>
            </div>
            
            
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="<?php echo BASE_URL; ?>/assets/modules/jquery.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/popper.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/tooltip.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/moment.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/stisla.js"></script>
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="<?php echo BASE_URL; ?>/assets/js/scripts.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/custom.js"></script>
</body>
</html>