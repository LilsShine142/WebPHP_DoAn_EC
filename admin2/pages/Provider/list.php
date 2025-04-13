<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap 5.3 Modal Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Import file pagination -->
    <script src="assets/Components/Pagination.js"></script>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Provider list</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-search d-flex align-items-center gap-4 p-4">
            <!-- Ô tìm kiếm -->
            <div class="position-relative w-75">
                <input type="text" id="keyword" name="keyword" formcontrolname="keyword"
                    placeholder="Enter the provider name you want to search......"
                    class="form-control ps-4">
                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted" style="cursor:pointer"></i>
            </div>

            <!-- Nút filter -->
            <button id="toggleFilter" class="btn d-flex align-items-center justify-content-center rounded"
                style="width: 40px; height: 40px; background-color: #d5e2d2; border: none;">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.249995 1.61C2.56999 4.59 5.99999 9 5.99999 9V14C5.99999 15.1 6.89999 16 7.99999 16C9.09999 16 10 15.1 10 14V9C10 9 13.43 4.59 15.75 1.61C16.26 0.95 15.79 0 14.95 0H1.03999C0.209995 0 -0.260005 0.95 0.249995 1.61Z" fill="#00A64F"></path>
                </svg>
            </button>

            <!-- Nút Add Provider -->
            <a href="index.php?page=pages/Provider/create.php" class="btn btn-primary btn-sm btn-add btn-lg px-4 py-2">
                <i class="fas fa-plus"></i> Add Provider
            </a>
        </div>
        <!-- Bộ lọc -->
        <div id="filterContainer" class="card p-3 my-4 mx-4" style="display: none;"> <!-- my-3: margin trên/dưới, mx-4: margin trái/phải -->
            <div _ngcontent-obg-c192="" class="d-flex align-items-center justify-content-between" bis_skin_checked="1">
                <h6 _ngcontent-obg-c192="">Filter</h6>
                <span id="closeIcon" class="btn-close" disabled aria-label="Close" style="cursor:pointer"></span>
            </div>
            <form id="filterForm" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Provider ID</label>
                        <input type="text" name="id" class="form-control" placeholder="Enter Provider ID" value="<?= $_GET['id'] ?? '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Contact information</label>
                        <input type="text" name="contact" class="form-control" placeholder="Enter phone number or email" value="<?= $_GET['contact'] ?? '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">From date</label>
                        <input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To day</label>
                        <input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date'] ?? '' ?>">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-success btn-filter">Apply</button>
                    <button type="button" id="resetFilter" class="btn btn-secondary resetFilter">Cancel</button>
                </div>
            </form>
        </div>
        <!-- JS ẩn hiện bộ lọc khi bấm nút lọc -->
        <!-- <script>
            document.getElementById("toggleFilter").addEventListener("click", function() {
                toggleFilter();
            });

            document.getElementById("closeIcon").addEventListener("click", function() {
                toggleFilter();
            });

            function toggleFilter() {
                let filterContainer = document.getElementById("filterContainer");
                let filterIcon = document.getElementById("filterIcon");
                let closeIcon = document.getElementById("closeIcon");

                if (filterContainer.style.display === "none") {
                    filterContainer.style.display = "block";
                    filterIcon.style.display = "none";
                    closeIcon.style.display = "inline";
                } else {
                    filterContainer.style.display = "none";
                    filterIcon.style.display = "inline";
                    closeIcon.style.display = "none";
                }
            }
        </script> -->
        <!-- Table -->
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Provider name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="provider-data-table">
                    <!-- CHÈN DỮ LIỆU TỪ SCRIPT  -->
                </tbody>
            </table>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div id="record-info" class="text-muted">Đang hiển thị 0-0 trên tổng số 0 mục</div>
                </div>
                <div class="col-md-6">
                    <div id="pagination-container" class="d-flex justify-content-end"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Provider -->
    <div class="modal fade" id="updateProviderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateProviderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProviderLabel">Update Provider Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateProviderForm" method="POST">

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="provider_id" name="provider_id">
                                </div>
                                <div class="form-group">
                                    <label for="provider_name">Provider Name:</label>
                                    <input type="text" class="form-control" id="provider_name" name="provider_name" placeholder="Enter provider name" required>
                                </div>

                                <div class="form-group">
                                    <label for="provider_email">Contact Email:</label>
                                    <input type="email" class="form-control" id="provider_email" name="provider_email" placeholder="Enter contact email" required>
                                </div>

                                <div class="form-group">
                                    <label for="provider_phone">Phone Number:</label>
                                    <input type="text" class="form-control" id="provider_phone" name="provider_phone" placeholder="Enter phone number" required>
                                </div>
                                <div class="form-group">
                                    <label for="provider_created_at" style="display: none;">Created At:</label>
                                    <input type="hidden" class="form-control" id="provider_created_at" name="provider_created_at">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-button">Close</button>
                    <button type="button" class="btn btn-primary" id="saveProvider">Save</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Toast container để hiển thị thông báo thành công -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <script src="assets/js/Provider/validationProvider.js"></script>
    <script src="assets/js/Provider/providerActions.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>