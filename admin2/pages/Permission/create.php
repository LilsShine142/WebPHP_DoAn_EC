<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Role</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Role</h2>
        <form id="roleForm">
            <!-- Nhập tên Role -->
            <div class="form-group">
                <label for="roleName">Role Name:</label>
                <input type="text" class="form-control" id="roleName" name="role_name" required>
            </div>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>Create</th>
                        <th>Read</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="permissionTableBody">
                    <!-- Data sẽ được load vào đây -->
                </tbody>
            </table>
            
            <button type="submit" class="btn btn-primary">Add Role</button>
            <a href="index.php?page=pages/Permission/role.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            // Load danh sách permissions từ API
            $.ajax({
                url: "http://localhost:81/WebPHP_DoAn_EC/api/users/permissions",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        let groupedPermissions = {};
                        response.data.forEach(permission => {
                            let key = permission.action_name.split(' ')[1];
                            let type = permission.action_name.split(' ')[0];
                            if (!groupedPermissions[key]) {
                                groupedPermissions[key] = { "Create": "", "Read": "", "Update": "", "Delete": "" };
                            }
                            groupedPermissions[key][type] = `<input type='checkbox' name='permissions[]' value='${permission.id}'>`;
                        });

                        let tableContent = "";
                        for (let key in groupedPermissions) {
                            tableContent += `<tr>
                                <td>${key}</td>
                                <td>${groupedPermissions[key]["Create"]}</td>
                                <td>${groupedPermissions[key]["Read"]}</td>
                                <td>${groupedPermissions[key]["Update"]}</td>
                                <td>${groupedPermissions[key]["Delete"]}</td>
                            </tr>`;
                        }
                        $("#permissionTableBody").html(tableContent);
                    }
                },
                error: function () {
                    alert("Failed to load permissions.");
                }
            });

            // Bắt sự kiện khi submit form
            $("#roleForm").submit(function (event) {
                event.preventDefault();
                
                let roleName = $("#roleName").val();
                let selectedPermissions = $("input[name='permissions[]']:checked").map(function () {
                    return $(this).val();
                }).get();

                if (!roleName) {
                    alert("Please enter a role name!");
                    return;
                }

                if (selectedPermissions.length === 0) {
                    alert("Please select at least one permission!");
                    return;
                }

                // Gửi request tạo Role
                $.ajax({
                    url: "http://localhost:81/WebPHP_DoAn_EC/api/users/roles",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ name: roleName }),
                    success: function (response) {
                        if (response.success) {
                            let roleId = response.data.id; // ID của Role mới tạo
                            
                            // Gửi từng permission vào bảng role_permissions
                            let permissionPromises = selectedPermissions.map(permissionId => {
                                return $.ajax({
                                    url: "http://localhost:81/WebPHP_DoAn_EC/api/users/role_permissions",
                                    method: "POST",
                                    contentType: "application/json",
                                    data: JSON.stringify({
                                        role_id: roleId,
                                        permission_id: permissionId
                                    })
                                });
                            });

                            // Chờ tất cả request hoàn tất
                            Promise.all(permissionPromises)
                                .then(() => {
                                    alert("Role created successfully!");
                                    window.location.href = "index.php?page=pages/Permission/role.php";
                                })
                                .catch(error => {
                                    console.error("Error assigning permissions:", error);
                                    alert("Role created but failed to assign permissions.");
                                });
                        } else {
                            alert("Failed to create role!");
                        }
                    },
                    error: function (xhr) {
                        let response = xhr.responseJSON;

                        if (response && response.code === "23000") {
                            alert("Role name already exists! Please choose another name.");
                        } else {
                            alert("Error creating role.");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
