<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Role Details</h2>
        <form>
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
                    <!-- Dữ liệu sẽ được load tại đây -->
                </tbody>
            </table>
        </form>
        <a href="index.php?page=pages/Permission/role.php" class="btn btn-secondary">Back</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            // Lấy role_id từ URL
            const urlParams = new URLSearchParams(window.location.search);
            const roleId = urlParams.get("id");

            if (!roleId) {
                alert("Role ID is missing!");
                window.location.href = "index.php?page=pages/Permission/role.php";
                return;
            }

            let allPermissions = {}; // Lưu danh sách permission dạng nhóm (Create, Read, Update, Delete)
            let assignedPermissions = new Set(); // Lưu các quyền đã cấp

            // Gọi cả hai API song song
            let permissionRequest = $.ajax({
                url: "http://localhost:81/WebPHP_DoAn_EC/api/users/permissions",
                method: "GET",
                dataType: "json"
            });

            let rolePermissionRequest = $.ajax({
                url: `http://localhost:81/WebPHP_DoAn_EC/api/users/role_permissions?role_id=${roleId}`,
                method: "GET",
                dataType: "json"
            });

            // Chờ cả hai API hoàn thành
            $.when(permissionRequest, rolePermissionRequest).done(function (permResponse, roleResponse) {
                let permissionData = permResponse[0].data; // API 1: Tất cả quyền
                let roleData = roleResponse[0].data; // API 2: Quyền đã cấp

                // Lưu danh sách quyền đã cấp
                roleData.forEach(rolePerm => {
                    assignedPermissions.add(rolePerm.permission_id);
                });

                // Xử lý tất cả permissions
                permissionData.forEach(permission => {
                    let [type, key] = permission.action_name.split(' '); // Phân tách loại hành động và tên quyền

                    if (!allPermissions[key]) {
                        allPermissions[key] = { "Create": "", "Read": "", "Update": "", "Delete": "" };
                    }

                    // Xác định nếu quyền đã được cấp (checked)
                    let isChecked = assignedPermissions.has(permission.id) ? "checked" : "";

                    // Gán giá trị cho từng nhóm quyền (Create, Read, Update, Delete)
                    allPermissions[key][type] = `<input type='checkbox' name='permissions[]' value='${permission.id}' ${isChecked} disabled>`;
                });

                // Render bảng quyền
                let tableContent = "";
                for (let key in allPermissions) {
                    tableContent += `<tr>
                        <td>${key}</td>
                        <td>${allPermissions[key]["Create"] || ""}</td>
                        <td>${allPermissions[key]["Read"] || ""}</td>
                        <td>${allPermissions[key]["Update"] || ""}</td>
                        <td>${allPermissions[key]["Delete"] || ""}</td>
                    </tr>`;
                }
                $("#permissionTableBody").html(tableContent);
            }).fail(function () {
                alert("Failed to load permissions or role data.");
            });
        });

    </script>
</body>
</html>
