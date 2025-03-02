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
        <div class="form-group">
            <label for="roleName">Role Name:</label>    
            <input type="text" class="form-control" id="roleName" name="role_name" required>    
        </div>        
        <form id="roleForm">
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
                <tbody id="permissionTableBody"></tbody>
            </table>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php?page=pages/Permission/role.php" class="btn btn-secondary">Back</a>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(async function () {
            const urlParams = new URLSearchParams(window.location.search);
            const roleId = urlParams.get("id");

            if (!roleId) {
                alert("Role ID is missing!");
                window.location.href = "index.php?page=pages/Permission/role.php";
                return;
            }

            let allPermissions = {};
            let assignedPermissions = new Set();

                let [roleResponse, permResponse] = await Promise.all([
                    $.get(`http://localhost:81/WebPHP_DoAn_EC/api/users/roles/${roleId}`),
                    $.get("http://localhost:81/WebPHP_DoAn_EC/api/users/permissions"),
                ]);

                if (roleResponse.success) {
                    $("#roleName").val(roleResponse.data.name);
                }

                let rolePermResponse = await $.get(`http://localhost:81/WebPHP_DoAn_EC/api/users/role_permissions?role_id=${roleId}`);
                rolePermResponse.data.forEach(rolePerm => {
                    assignedPermissions.add(rolePerm.permission_id);
                });

                if (permResponse.success && Array.isArray(permResponse.data) && permResponse.data.length > 0) {
                    permResponse.data.forEach(permission => {
                        let [type, key] = permission.action_name.split(' ');
                        if (!allPermissions[key]) {
                            allPermissions[key] = { "Create": "", "Read": "", "Update": "", "Delete": "" };
                        }
                        let isChecked = assignedPermissions.has(permission.id) ? "checked" : "";
                        allPermissions[key][type] = `<input type='checkbox' name='permissions[]' value='${permission.id}' ${isChecked}>`;
                    });

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
                } else {
                    $("#permissionTableBody").html(`<tr><td colspan="5" class="text-center">No permissions available</td></tr>`);
                }

            $("#roleForm").submit(async function (event) {
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

                $("#roleForm button[type='submit']").prop("disabled", true).text("Updating...");

                try {
                    await $.ajax({
                        url: `http://localhost:81/WebPHP_DoAn_EC/api/users/roles/${roleId}`,
                        method: "PUT",
                        contentType: "application/json",
                        data: JSON.stringify({ name: roleName })
                    });

                    await $.ajax({ url: `http://localhost:81/WebPHP_DoAn_EC/api/users/role_permissions?role_id=${roleId}`, method: "DELETE" });

                    let permissionPromises = selectedPermissions.map(permissionId => {
                        return $.ajax({
                            url: "http://localhost:81/WebPHP_DoAn_EC/api/users/role_permissions",
                            method: "POST",
                            contentType: "application/json",
                            data: JSON.stringify({ role_id: roleId, permission_id: permissionId })
                        });
                    });

                    if (permissionPromises.length > 0) await Promise.all(permissionPromises);

                    alert("Role updated successfully!");
                    window.location.href = "index.php?page=pages/Permission/role_update.php&id=" + roleId;
                } catch (error) {
                    alert("Error updating role.");
                }

                $("#roleForm button[type='submit']").prop("disabled", false).text("Update");
            });
        });
    </script>
</body>
</html>
