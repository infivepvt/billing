<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar Menu</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .main-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      z-index: 1000;
      background-color: #1BA664;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    .sidebar-menu { padding: 0 10px; }
    .sidebar-brand img { max-width: 100%; height: auto; }
    .menu-item a:hover {
      background-color: rgba(255, 255, 255, 0.15) !important;
      transform: translateX(5px);
    }
    .menu-item.active a {
      background-color: rgba(255, 255, 255, 0.2) !important;
      font-weight: 600 !important;
    }
    @media (max-width: 991px) {
      .main-sidebar { width: 200px; }
    }
    @media (max-width: 767px) {
      .main-sidebar { width: 150px; }
      .sidebar-menu { font-size: 14px; }
      .menu-item a { padding: 10px 15px; }
      .sidebar-brand img { width: 60px; }
    }
  </style>
</head>
<body>
  <div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand text-center" style="padding: 20px 0;">
        <img src="<?php echo BASE_URL; ?>/assets/img/logo.png" style="width: 80px; height: auto; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
      </div>
      <ul class="sidebar-menu" style="margin-top: 30px;">
        <li class="menu-header" style="padding: 10px 25px; font-size: 12px; color: rgba(255,255,255,0.7);">MAIN NAVIGATION</li>
        <li class="menu-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>/dashboard" style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
            <i class="fas fa-tachometer-alt" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
            <span style="color: white; font-size: 15px; font-weight: 500;">Dashboard</span>
          </a>
        </li>
        <li class="menu-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>/product" style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
            <i class="fas fa-box-open" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
            <span style="color: white; font-size: 15px; font-weight: 500;">Products</span>
          </a>
        </li>
        <li class="menu-header" style="padding: 15px 25px 10px; font-size: 12px; color: rgba(255,255,255,0.7);">ACCOUNT</li>
        <li class="menu-item" style="margin-top: 30px;">
          <a class="nav-link" href="./" style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s; background-color: rgba(255,255,255,0.1);">
            <i class="fas fa-sign-out-alt" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
            <span style="color: white; font-size: 15px; font-weight: 500;">Logout</span>
          </a>
        </li>
        <li class="menu-item">
          <a class="nav-link" href="https://infiveprint.com/job/" style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
            <i class="fas fa-tasks" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
            <span style="color: white; font-size: 15px; font-weight: 500;">Job Status</span>
          </a>
        </li>
        <li class="menu-header">Quotations</li>
        <li class="menu-item">
          <a class="nav-link" href="quotation_history.php" style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
            <i class="fas fa-file-invoice" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
            <span style="color: white; font-size: 15px; font-weight: 500;">Quotation History</span>
          </a>
        </li>
        <li class="menu-item">
          <a class="nav-link" href="card_layout.php" style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
            <i class="fas fa-file-invoice" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
            <span style="color: white; font-size: 15px; font-weight: 500;">Card Layout</span>
          </a>
        </li>
      </ul>
    </aside>
  </div>
</body>
</html>