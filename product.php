<?php
session_start();
include('db.php'); 

if( $_SESSION['logged_id'] <= 0)
{
  header('Location: ./');
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
  <style type="text/css">
    body {
            font-family: 'Montserrat', sans-serif;
        }

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

        .product{
          font-size: 16px;
          font-weight: bold;
        }

        .btn-success{
            background-color: #1BA664; /* Custom green color */
            border-color: #1BA664; /* Custom green color */
            color: #fff; /* Ensure text color remains white */
        }
  </style>


</head>

<body>
  
  <div class="col-md-12 offset-md-0" >
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
          <br><hr><br>

          <div class="row">
            <form method="post" action="./addproduct">

                  <input type="submit" name="btnSubmit" value="Add Product" class="btn btn-success">
                </form>
          </div>
          <div class="section-body" >
            

            <table class="table table-bordered col-md-6">
              <tr>
                <th class="col-md-3">Product Name</th>
                <th class="col-md-3">Delete</th>
                
            </tr>

            <?php

            if( isset($_POST['btnDelete']) )
            {
              extract($_POST);
             
              $statement = $pdo->prepare("DELETE FROM pixel_media_product WHERE id = ? ");
              $statement->execute(array($id));
            }

            $statement = $pdo->prepare("SELECT * FROM pixel_media_product ORDER BY created_at ");
            $statement->execute();
            $products = $statement->fetchAll(PDO::FETCH_ASSOC);
             foreach($products as $item) { 
            ?>
            
            <tr>
                <td class="col-md-3"><?php echo $item['product_name']; ?></td>
                <th class="col-md-3">
                  <form method="post" action="./product">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                  <input type="submit" value="Delete" name="btnDelete" class="btn btn-success">
                </form>
                </th>
                
            </tr>

          <?php  } ?>
          </table>
          
          </div>



        </section>
      </div>
      <?php //include('footer.php'); ?>
    </div>
  </div>


  <!-- General JS Scripts -->
  <script src="<?php echo BASE_URL; ?>/assets/modules/jquery.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/popper.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/tooltip.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/modules/moment.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/stisla.js"></script>
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="<?php echo BASE_URL; ?>/assets/js/scripts.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/custom.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
    // Add click event listeners to the buttons
    $('#activeButton').click(function() {
        // Change the class of activeButton to btn-success
        $(this).removeClass('btn-default').addClass('btn-success');
        
        // Change the class of deliveredButton and nonePaidButton to btn-default
        $('#deliveredButton, #nonePaidButton').removeClass('btn-success').addClass('btn-default');
    });

    $('#deliveredButton').click(function() {
        // Change the class of deliveredButton to btn-success
        $(this).removeClass('btn-default').addClass('btn-success');
        
        // Change the class of activeButton and nonePaidButton to btn-default
        $('#activeButton, #nonePaidButton').removeClass('btn-success').addClass('btn-default');
    });

    $('#nonePaidButton').click(function() {
        // Change the class of nonePaidButton to btn-success
        $(this).removeClass('btn-default').addClass('btn-success');
        
        // Change the class of activeButton and deliveredButton to btn-default
        $('#activeButton, #deliveredButton').removeClass('btn-success').addClass('btn-default');
    });
});

    function add_new_order()
    {
      window.location.href = './addOrder';
    }

    function view_order(order_id)
    {
      window.location.href = './viewOrder/'+order_id;
    }

    function download_invoice(order_id){
      window.location.href = './report/'+order_id;
    }

  </script>
</body>
</html>