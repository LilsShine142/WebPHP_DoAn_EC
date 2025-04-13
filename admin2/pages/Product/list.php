<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Thêm vào phần head -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/simplePagination.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.min.js"></script>
    <script src="assets/Components/Pagination.js"></script>

</head>

<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Customer list</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-search" style="display: flex; margin: 15px;">
            <div class="search" style="width: 80%; margin-right: 30px;">
                <input type="text" class="form-control" placeholder="Search...">
            </div>
            <a href="index.php?page=pages/Product/create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add new product
            </a>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Product name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Stock quantity</th>
                        <th>Stop selling</th>
                        <th>View product variations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="product-table">

                </tbody>
            </table>
            <div class="d-flex flex-wrap justify-content-between align-items-end px-2 pt-1 pb-0 bg-white">
                <div id="record-info" class="small text-muted mb-1 mb-sm-0 me-2">
                    Showing <span class="fw-medium">0-0</span> of <span class="fw-medium">0</span>
                </div>
                <div id="pagination-container" class="mt-4">
                    <nav aria-label="Page navigation" class="pb-0">
                        <ul class="pagination pagination-sm mb-0 pb-0">
                            <!-- pagination items -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- ModalView -->
    <div class="modal fade" id="modalView" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <table id="viewProductTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-start bg-dark text-white p-2" colspan="2">Product Information</th>
                                </tr>
                            </thead>
                            <tbody id="data-viewproduct-table">
                                <tr>
                                    <td class="fw-bold">Product ID</td>
                                    <td id="product_id"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Product Name</td>
                                    <td id="product_name"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Model</td>
                                    <td id="model"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Description</td>
                                    <td id="description"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update -->
    <div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUpdateLabel">Update Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form id="updateProductForm" enctype="multipart/form-data">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-start bg-dark text-white p-2" colspan="2">Product Information</th>
                                        <input type="hidden" id="productId" value="">
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold col-4">Product Name</td>
                                        <td><input type="text" id="product_name" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Brand ID</td>
                                        <td><input type="number" id="brand_id" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Model</td>
                                        <td><input type="text" id="model" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Category ID</td>
                                        <td><input type="number" id="category_id" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Description</td>
                                        <td><textarea id="description" class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Image Name</td>
                                        <td>
                                            <input type="text" id="image_name" class="form-control" readonly>
                                            <input type="file" id="image_upload" class="form-control mt-2" accept="image/*">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Stop Selling</td>
                                        <td>
                                            <select id="stop_selling" class="form-control">
                                                <option value="false">No</option>
                                                <option value="true">Yes</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

    <script src="assets/js/Product/Validation.js"></script>
    <script src="assets/js/Product/ProductAction.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>

</html>