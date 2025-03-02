<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <button type="button" class="btn btn-primary" style="margin: 15px 20px -20px;" onclick="window.location.href='index.php?page=pages/Customer/list.php'">Quay về</button>
    <div class="container mt-5">
        <h2>Thêm Người dùng Mới</h2>
        <form action="index.php?page=pages/Customer/create.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="customerId" name="customerId">

            <div class="form-group">
                <label for="fullname">Họ và tên:</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="province">Tỉnh/Thành phố:</label>
                <select class="form-control" id="province" name="province" required></select>
            </div>

            <div class="form-group">
                <label for="district">Quận/Huyện:</label>
                <select class="form-control" id="district" name="district" required></select>
            </div>

            <div class="form-group">
                <label for="ward">Phường/Xã:</label>
                <select class="form-control" id="ward" name="ward" required></select>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ chi tiết:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <button type="submit" class="btn btn-primary" id="saveCustomer">Thêm Người dùng</button>
        </form>
    </div>
    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Customer/validationCustomer.js"></script>
    <script src="assets/js/Customer/customerActions.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>