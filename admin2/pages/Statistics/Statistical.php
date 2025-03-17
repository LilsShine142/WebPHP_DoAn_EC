<?php
array_push($cssStack, '<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">');
array_push($cssStack, '<link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">');


array_push($jsStack, '
    <script>
      $.widget.bridge("uibutton", $.ui.button);
    </script>');
array_push($jsStack, '<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>');
array_push($jsStack, '<script src="plugins/chart.js/Chart.min.js"></script>');
array_push($jsStack, '<script src="plugins/sparklines/sparkline.js"></script>');
array_push($jsStack, '<script src="plugins/jqvmap/jquery.vmap.min.js"></script> ');
array_push($jsStack, '<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>');
array_push($jsStack, '<script src="plugins/jquery-knob/jquery.knob.min.js"></script>');
array_push($jsStack, '<script src="plugins/moment/moment.min.js"></script>');
array_push($jsStack, '<script src="plugins/daterangepicker/daterangepicker.js"></script>');

array_push($jsStack, '<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>');
array_push($jsStack, '<script src="plugins/summernote/summernote-bs4.min.js"></script>');
array_push($jsStack, '<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>');
?>

<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- /.content-header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard v1</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
          <div class="inner">
            <h3 class="totalOrders">
            </h3>
            <p>Total Orders</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a href="index.php?page=pages/Order/list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner">
            <h3 class="totalProducts">53<sup style="font-size: 20px">%</sup></h3>

            <p>Total Product</p>
          </div>
          <div class="icon">
            <i class="fas fa-box-open"></i>
          </div>
          <a href="index.php?page=pages/Product/list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
          <div class="inner">
            <h3 class="totalUsers">44</h3>

            <p>User Registrations</p>
          </div>
          <div class="icon">
            <i class="ion ion-person-add"></i>
          </div>
          <a href="index.php?page=pages/User/list.php&type=customer" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
          <div class="inner">
            <h3 class="totalRevenue">150.000.000 VNĐ</h3> <!-- Giả sử tổng doanh thu là 65,000 USD -->
            <p>Total Revenue</p>
          </div>
          <div class="icon">
            <i class="ion ion-cash"></i> <!-- Đổi icon thành biểu tượng tiền tệ -->
          </div>
          <a href="index.php?page=pages/Order/list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->
    <!-- Main row -->
    <div class="row">
      <section class="col-lg-12 connectedSortable">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-chart-pie mr-1"></i> Revenue & Expense Statistics
            </h3>
            <!-- Dropdown for filter selection -->
            <div class="card-tools">
              <select id="chartFilter">
                <option value="filter-year">Filter by Year</option>
                <option value="filter-month">Filter by Month</option>
              </select>
            </div>
          </div>

          <div class="card-body">
            <!-- Year Picker -->
            <div id="yearPickerContainer" class="mb-3">
              <label for="yearPicker">Select Year:</label>
              <input type="number" id="yearPicker" name="yearPicker" min="2000" max="2100" step="1" value="<?= date('Y'); ?>">
            </div>

            <!-- Month Picker -->
            <div id="monthPickerContainer" class="mb-3" style="display: none;">
              <label for="monthPicker">Select Month:</label>
              <?php
              $currentMonth = date('Y-m'); // Get current year-month (YYYY-MM)
              ?>
              <input type="month" id="monthPicker" name="monthPicker" value="<?php echo $currentMonth; ?>">
            </div>

            <!-- Chart -->
            <div class="tab-content p-0">
              <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                <canvas id="revenue-chart-canvas" height="300"></canvas>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="table">
        <h4 class="title">Sales Product Statistics Table</h4>
        <table class="table table-striped table-hover" id="product-table">
          <thead>
            <tr>
              <th>Date / Month</th>
              <th>Product Name</th>
              <th>Color</th>
              <th>Size</th>
              <th>Quantity</th>
              <th>Stock quantity</th>
              <th>Status</th> <!-- Đang bán / Ngừng bán (stop_selling)-->
              <th>Unit Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody class="product-table-body" id="product-table-body">
            <!-- Product data will be updated here -->
          </tbody>
        </table>
      </div>
    </div>


    <!-- /.row (main row) -->
  </div><!-- /.container-fluid -->
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/Statistics/statisticsActions.js"></script>