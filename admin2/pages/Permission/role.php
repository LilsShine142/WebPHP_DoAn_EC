<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management</title>

    <!-- Thêm Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

    <!-- Thêm jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role list</h3>

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
                    <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                </div>
                <a href="index.php?page=pages/Permission/create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Role
                </a>
            </div>

            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Role ID</th>
                            <th>Role name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="roleTableBody">
                        <!-- Dữ liệu sẽ được AJAX tải vào đây -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript xử lý AJAX -->
    <script>
        $(document).ready(function() {
            loadRoles(); // Gọi hàm load dữ liệu khi trang tải

            function loadRoles() {
                $.ajax({
                    url: `${BASE_API_URL}/api/users/roles`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.length > 0) {
                            let roles = response.data;
                            let html = "";
                            roles.forEach(function(role) {
                                html += `<tr>
                                    <td>${role.id}</td>
                                    <td>${role.name}</td>
                                    <td>
                                        <a href='index.php?page=pages/Permission/role_details.php&id=${role.id}' class='btn btn-info btn-sm' title='View'>
                                            <i class='fas fa-eye'></i>
                                        </a>
                                        <a href='index.php?page=pages/Permission/role_update.php&id=${role.id}' class='btn btn-warning btn-sm' title='Edit'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <button class='btn btn-danger btn-sm' title='Delete' onclick='deleteRole(${role.id})'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>`;
                            });
                            $("#roleTableBody").html(html);
                        } else {
                            $("#roleTableBody").html("<tr><td colspan='3' class='text-center'>No roles found</td></tr>");
                        }
                    },
                    error: function() {
                        $("#roleTableBody").html("<tr><td colspan='3' class='text-center text-danger'>Failed to load data</td></tr>");
                    }
                });
            }

            // Xóa role
            window.deleteRole = function(roleId) {
                if (confirm("Are you sure you want to delete this role?")) {
                    $.ajax({
                        url: `${BASE_API_URL}/api/users/role_permissions?role_id=${roleId}`,
                        type: "DELETE"
                    });
                    $.ajax({
                        url: `${BASE_API_URL}/api/users/roles/${roleId}`,
                        type: "DELETE",
                        success: function(response) {
                            if (response.success) {
                                alert("Role deleted successfully");
                                loadRoles();
                            } else {
                                alert("Failed to delete role");
                            }
                        },
                        error: function() {
                            alert("Failed to delete role");
                        }
                    });
                }
            }

            // Tìm kiếm Role
            $("#searchInput").on("keyup", function() {
                let value = $(this).val().toLowerCase();
                $("#roleTableBody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>

</body>

</html>