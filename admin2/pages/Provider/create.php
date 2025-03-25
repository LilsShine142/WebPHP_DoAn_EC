<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <button type="button" class="btn btn-primary" style="margin: 15px 20px -20px;" onclick="window.location.href='index.php?page=pages/Provider/list.php'">
        <i class="bi bi-arrow-left"></i> Back
    </button>
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card border-primary shadow-lg rounded-4 w-75">
            <div class="card-header bg-primary text-white text-center rounded-top-4">
                <h4 class="mb-0"><i class="bi bi-person-plus-fill"></i> Add Provider</h4>
            </div>
            <div class="card-body">
                <form id="addProviderForm" method="POST">
                    <input type="hidden" id="provider_id" name="provider_id">

                    <div class="form-group mb-3">
                        <label for="provider_name" class="fw-bold">Provider Name:</label>
                        <input type="text" class="form-control border-primary" id="provider_name" name="provider_name" placeholder="Enter provider name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="provider_email" class="fw-bold">Contact Email:</label>
                        <input type="email" class="form-control border-primary" id="provider_email" name="provider_email" placeholder="Enter contact email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="provider_phone" class="fw-bold">Phone Number:</label>
                        <input type="text" class="form-control border-primary" id="provider_phone" name="provider_phone" placeholder="Enter phone number" required>
                    </div>

                    <div class="form-group">
                        <label for="provider_created_at" style="display: none;">Created At:</label>
                        <input type="hidden" class="form-control" id="provider_created_at" name="provider_created_at">
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-success btn-lg w-100" id="addProviderBtn">
                            <i class="bi bi-plus-lg"></i> Add Provider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Provider/validationProvider.js"></script>
    <script src="assets/js/Provider/providerActions.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
</body>

</html>