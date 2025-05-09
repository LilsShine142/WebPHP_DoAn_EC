<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $cssStack = [];
  $jsStack = [];
  $selectedContent = isset($_GET['page']) ? $_GET['page'] : 'index';
  $contentPath = strtok($selectedContent, '?'); // Loại bỏ query string
  require_once __DIR__ . "/../api/config/constants.php";
  include("layouts/head.php");
  ?>
  <script>
    // Gán BASE_API_URL cho biến JS từ PHP
    const BASE_API_URL = "<?php echo BASE_API_URL; ?>";

    // Kiểm tra đăng nhập khi trang tải xong
    document.addEventListener('DOMContentLoaded', function() {
      const user = JSON.parse(localStorage.getItem('user'));
      console.log("user", user);
      // Nếu không có user hoặc không phải admin (role_id = 1)
      
    });
  </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- navbar -->
    <?php include("layouts/navbar.php"); ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include("layouts/sidebar.php"); ?>

    <!-- /.sidebar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <?php
      $baseDir = __DIR__;
      $contentPath = $baseDir . "/" . $contentPath;

      if (file_exists($contentPath)) {
        include($contentPath);
      } else {
        // echo "<pre>File not found: " . htmlspecialchars($contentPath) . "</pre>";
        include("pages/Statistics/Statistical.php");
      }
      ?>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    <?php include("layouts/footer.php"); ?>
    <!-- /.Footer-->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->


  <?php include("layouts/footlink.php"); ?>
  <?php
  // CSS
  foreach ($cssStack as $item) {
    echo "{$item}";
  }

  //JS
  foreach ($jsStack as $item) {
    echo "{$item}";
  }
  ?>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="dist/js/pages/dashboard.js"></script>
</body>

</html>