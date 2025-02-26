<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Thêm Sản Phẩm Mới</h2>
        <form action="process_create.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="productName">Tên Sản Phẩm:</label>
                <input type="text" class="form-control" id="productName" name="productName" required>
            </div>
            <div class="form-group">
                <label for="productDescription">Mô Tả Sản Phẩm:</label>
                <textarea class="form-control" id="productDescription" name="productDescription" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="productPrice">Giá Sản Phẩm:</label>
                <input type="number" class="form-control" id="productPrice" name="productPrice" required>
            </div>
            <div class="form-group">
                <label for="productImage">Hình Ảnh Sản Phẩm:</label>
                <input type="file" class="form-control-file" id="productImage" name="productImage" required>
            </div>
            <div class="form-group">
                <label for="productCategory">Danh Mục Sản Phẩm:</label>
                <select class="form-control" id="productCategory" name="productCategory" required>
                    <option value="">Chọn danh mục</option>
                    <option value="category1">Danh Mục 1</option>
                    <option value="category2">Danh Mục 2</option>
                    <option value="category3">Danh Mục 3</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Thêm Sản Phẩm</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>