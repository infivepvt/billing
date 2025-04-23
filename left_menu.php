<div class="main-sidebar sidebar-style-2" style="background-color:rgb(31, 163, 102);">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand text-center" style="padding: 20px 0;">
      <img src="<?php echo BASE_URL; ?>/assets/img/logo.png"
        style="width: 80px; height: auto; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    </div>

    <ul class="sidebar-menu" style="margin-top: 30px;">
      <li class="menu-header" style="padding: 10px 25px; font-size: 12px; color: rgba(255,255,255,0.7);">MAIN NAVIGATION
      </li>
      <li class="menu-item">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/dashboard"
          style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
          <i class="fas fa-tachometer-alt"
            style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
          <span style="color: white; font-size: 15px; font-weight: 500;">Dashboard</span>
        </a>
      </li>
      <li class="menu-item">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/product"
          style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
          <i class="fas fa-box-open" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
          <span style="color: white; font-size: 15px; font-weight: 500;">Products</span>
        </a>
      </li>
      <li class="menu-header" style="padding: 15px 25px 10px; font-size: 12px; color: rgba(255,255,255,0.7);">ACCOUNT
      </li>
      <li class="menu-item" style="margin-top: 30px;">
        <a class="nav-link" href="./"
          style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s; background-color: rgba(255,255,255,0.1);">
          <i class="fas fa-sign-out-alt" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
          <span style="color: white; font-size: 15px; font-weight: 500;">Logout</span>
        </a>
      </li>

      <li class="menu-item">
        <a class="nav-link" href="https://infiveprint.com/job/"
          style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
          <i class="fas fa-tasks" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
          <span style="color: white; font-size: 15px; font-weight: 500;">Job Status</span>
        </a>
      </li>
      <li class="menu-header">Quotations</li>
      <li class="menu-item">
        <a class="nav-link" href="quotation_history.php"
          style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
          <i class="fas fa-file-invoice" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
          <span style="color: white; font-size: 15px; font-weight: 500;">Quotation History</span>
        </a>
      </li>
      <li class="menu-item">
        <a class="nav-link" href="card_layout.php"
          style="padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s;">
          <i class="fas fa-file-invoice" style="width: 24px; text-align: center; margin-right: 10px; color: white;"></i>
          <span style="color: white; font-size: 15px; font-weight: 500;">Card Layout</span>
        </a>
      </li>

    </ul>

    <div class="sidebar-footer"
      style="position: absolute; bottom: 20px; width: 100%; text-align: center; color: rgba(255,255,255,0.7); font-size: 12px;">
      © <?php echo date('Y'); ?> Infive Print
    </div>
  </aside>
</div>

<style>
  .main-sidebar {
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  }

  .menu-item a:hover {
    background-color: rgba(255, 255, 255, 0.15) !important;
    transform: translateX(5px);
  }

  .menu-item.active a {
    background-color: rgba(255, 255, 255, 0.2) !important;
    font-weight: 600 !important;
  }

  .sidebar-menu {
    padding: 0 10px;
  }
</style>