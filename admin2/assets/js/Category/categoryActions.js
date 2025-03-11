document.addEventListener("DOMContentLoaded", function () {

    //=========================== Hàm hiển thị thông báo toast =====================================
    function toast(message, type = "success") {
        console.log("Toast function called with:", message, type); // Kiểm tra xem có gọi được không

        let colors = {
            success: "#28a745", // Xanh lá
            error: "#dc3545", // Đỏ
            warning: "#ffc107", // Vàng
            info: "#17a2b8" // Xanh dương
        };

        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: colors[type] || "#343a40", // Mặc định là màu xám
        }).showToast();
    }

    //====================================== Hiện modal và data khi bấm update ====================================
    // Lấy danh sách tất cả các nút có class "btn-update"
    //let lastCateId = null;
    $(document).on("click", ".btn-update", function () {

        let cateId = this.getAttribute("data-id");
        console.log("ID cate cần cập nhật:", cateId);
        $.ajax({
            url: `${BASE_API_URL}/api/products/categories/${cateId}`,
            type: "GET",
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                console.log("API Response Data:", response.data);
                if (response.data) {
                    // Điền dữ liệu vào form
                    $("#cateId").val(response.data.id);
                    $("#cate_name").val(response.data.name);
                    // Hiển thị modal
                    $("#updateCateModal").modal("show");
                }
            },
            error: function (error) {
                console.error("Lỗi API:", error);
            }
        });
    });



    //==================================== CẬP NHẬT THÔNG TIN DANH MỤC ====================================    
    const saveButton = document.getElementById("saveCate");

    saveButton.addEventListener("click", async function () {
        console.log("===> Bắt đầu sự kiện click");

        let cateIdUpdate = document.getElementById("cateId").value;
        console.log("ID danh mục cần cập nhật:", cateIdUpdate);

        let isValid = await validateCategory(cateIdUpdate); // Chờ kiểm tra danh mục
        if (!isValid) return; // Nếu không hợp lệ thì dừng lại
        // Lấy dữ liệu từ form
        let cateDataUpdate = {
            name: document.getElementById("cate_name").value.trim() // Đúng ID của input name
        };

        console.log("Dữ liệu cập nhật:", cateDataUpdate);

        // API endpoint
        let apiCateUpdate = `${BASE_API_URL}/api/products/categories/${cateIdUpdate}`;

        $.ajax({
            url: apiCateUpdate,
            type: 'PUT',
            data: JSON.stringify(cateDataUpdate),
            contentType: "application/json",
            success: function (response) {
                console.log("Cập nhật thành công:", response);
                if (response.success) {
                    toast("Cập nhật danh mục thành công!", "success"); // Hiển thị thông báo

                    $("#updateCateModal").modal("hide"); // Đóng modal

                    // Cập nhật lại danh sách danh mục mà không load lại trang
                    updateCateRow(cateIdUpdate, cateDataUpdate);
                }
            },
            error: function (xhr) {
                alert("Có lỗi xảy ra khi cập nhật danh mục!");
                console.error("AJAX Error:", xhr.responseText);
            }
        });
    });


    // Hàm cập nhật lại dữ liệu trên bảng mà không cần reload trang
    function updateCateRow(cateId, cateData) {
        let cateRow = document.querySelector(`#cate-${cateId}`);
        if (cateRow) {
            cateRow.querySelector(".cate-name").textContent = cateData.name;
            cateRow.querySelector(".cate-status").textContent = cateData.status;
            console.log(`Cập nhật danh mục #${cateId} thành công!`);
        } else {
            console.warn(`Không tìm thấy hàng danh mục #${cateId} trong bảng!`);
        }
    }

    //========================THÊM DANH MỤC ========================
    const showAddModal = document.getElementById("addCate");
    showAddModal.addEventListener("click", function () {
        $("#addCateModal").modal("show");
    });

    const addCateButton = document.getElementById("saveCateAdd");
    addCateButton.addEventListener("click", async function () {
        let cateDataNew = {
            name: document.getElementById("catename").value.trim(),
            status: document.getElementById("status").value
        };
        console.log("Danh mục mới:", cateDataNew);

        try {
            let isValid = await validateCategory(); // Chờ kiểm tra danh mục
            if (!isValid) return; // Nếu không hợp lệ thì dừng lại

            let apiCateCreate = `${BASE_API_URL}/api/products/categories`;
            $.ajax({
                url: apiCateCreate,
                type: "POST",
                data: JSON.stringify(cateDataNew),
                contentType: "application/json",
                success: function (response) {
                    console.log("Thêm mới thành công:", response);
                    if (response.success) {
                        toast("Thêm mới danh mục thành công!", "success");

                        // Đóng modal
                        $("#addCateModal").modal("hide");

                        // Xóa dữ liệu trong form
                        document.getElementById("catename").value = "";
                        document.getElementById("status").value = "";
                    }
                },
                error: function (xhr) {
                    alert("Có lỗi xảy ra khi thêm mới danh mục!");
                    console.error("AJAX Error:", xhr.responseText);
                }
            });
        } catch (error) {
            console.error("Lỗi kiểm tra danh mục:", error);
        }
    });



    //========================XÓA DANH MỤC ========================
    // Lấy danh sách tất cả các nút có class "btn-delete
    $(document).on("click", ".btn-delete", function () {
        let cateId = this.getAttribute("data-id");
        if (confirm("Bạn có chắc chắn muốn xóa mục này?")) {
            $.ajax({
                //Gửi api xóa người dùng
                url: `${BASE_API_URL}/api/products/categories/${cateId}`,
                type: "DELETE",
                success: function (response) {
                    try {
                        if (response.success) {
                            console.log("Xóa thành công:", response);
                            toast("Xóa danh mục thành công!", "success");
                            // Xóa dòng có id="cate-{id}"
                            let rowToDelete = document.getElementById(`cate-${cateId}`);
                            if (rowToDelete) rowToDelete.remove();

                        } else {
                            alert("Lỗi: " + res.message);
                        }
                    } catch (e) {
                        alert("Lỗi phản hồi từ server!");
                        console.error("JSON Error:", e);
                    }
                },
                error: function (xhr) {
                    alert("Có lỗi xảy ra khi xử lý yêu cầu!");
                    console.error("AJAX Error:", xhr.responseText);
                }
            });
        }

    });

    // //======================== LỌC DANH MỤC ========================
    // let searchTimeout;
    // //Tìm kiếm theo tên khi nhập vào ô input
    // $("#name").on("input", function () {
    //     clearTimeout(searchTimeout);
    //     searchTimeout = setTimeout(() => {
    //         let nameValue = $(this).val().trim();
    //         console.log("Name value:", nameValue);
    //         filterData(nameValue); // Gọi hàm lọc dữ liệu khi nhập tên
    //     }, 200); // Đợi 300ms sau khi người dùng dừng nhập mới gọi API
    // });

    // // Lấy danh sách tất cả các nút có class "btn-delete
    // $(document).on("click", ".btn-filter_cate", function () {
    //     console.log("Click filter button");
    //     //e.preventDefault();
    //     // Lấy giá trị từ ô nhập tên danh mục
    //     // let nameValue = $("#name").val().trim();
    //     // console.log("Name value:", nameValue);
    //     // Lấy dữ liệu từ form
    //     let formData = $("#filter_cateForm").serialize();

    //     // // Nếu có name, thêm vào formData
    //     // if (nameValue) {
    //     //     formData += `&name=${encodeURIComponent(nameValue)}`;
    //     // }

    //     console.log("Form data:", formData);

    //     filterData(nameValue, formData); // Gọi hàm lọc dữ liệu
    // });
    // function filterData(nameValue = "", extraParams = "") {
    //     let apiUrl = `${BASE_API_URL}/api/products/categories?name=${encodeURIComponent(nameValue)}`;
    //     console.log("API URL:", apiUrl);
    //     if (extraParams) {
    //         apiUrl += `&${extraParams}`;
    //     }
    //     console.log("API URL with extra params:", apiUrl);

    //     $.ajax({
    //         url: apiUrl,
    //         type: "GET",
    //         success: function (response) {
    //             console.log("Filtered Data:", response);
    //             renderCateListTable(response.data); // Hiển thị lại bảng với dữ liệu mới
    //         },
    //         error: function (err) {
    //             console.error("Error fetching data:", err);
    //         },
    //     });
    // }


    // // Xóa bộ lọc
    // $("#resetFilter").click(function () {
    //     console.log("Click reset filter button");
    //     $("#filterForm")[0].reset();
    // });

    // // Hàm cập nhật danh sách người dùng
    // // Hàm cập nhật danh sách người dùng
    // function renderCateListTable(categories) {
    //     let tableBody = document.getElementById('cate-data-table');

    //     console.log("Danh sách danh mục:", categories);
    //     console.log("table", tableBody);

    //     // Xóa nội dung cũ
    //     tableBody.innerHTML = "";

    //     // Kiểm tra nếu mảng trống
    //     if (!Array.isArray(categories) || categories.length == 0) {
    //         tableBody.innerHTML = `<tr><td colspan='8' class='text-center'>No data available</td></tr>`;
    //         return;
    //     }

    //     // Tạo nội dung bảng
    //     tableBody.innerHTML = categories.map(cate => `
    //     <tr id="cate-${cate.id}">
    //         <td>${cate.id}</td>
    //         <td class="cate-name">${cate.name}</td>
    //         <td class="cate-status">${cate.status}</td>
    //         <td>
    //             <button class="btn btn-warning btn-update" data-id="${cate.id}">
    //                 <i class="fas fa-edit"></i>
    //             </button>
    //             <button class="btn btn-danger btn-delete" data-id="${cate.id}">
    //                 <i class="fas fa-trash"></i>
    //             </button>
    //         </td>
    //     </tr>
    // `).join('');
    // }

});
