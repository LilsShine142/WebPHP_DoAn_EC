document.addEventListener("DOMContentLoaded", function () {
    function getCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}`;
    }
    // =============================================== THÊM NHÀ CUNG CẤP ===============================================
    $(document).on('click', '#addProviderBtn', async function () {
        console.log("===> Bắt đầu sự kiện click");
        //e.preventDefault(); // Ngăn trang load lại
        let modal = document.querySelector("#addProviderForm");
        let provider = getProviderDataFromForm(modal);
        console.log("Dữ liệu nhà cung cấp cần thêm:", provider);

        // Kiểm tra dữ liệu
        if (! await validateProvider(provider, false)) {
            return;
        }

        // Gọi API thêm nhà cung cấp
        callAPIAddProvider(provider);
    });

    // Hàm lấy dữ liệu từ form 
    function getProviderDataFromForm(modal) {

        let provider = {
            id: modal.querySelector("#provider_id").value.trim(),
            full_name: modal.querySelector("#provider_name").value.trim(),
            email: modal.querySelector("#provider_email").value.trim(),
            phone_number: modal.querySelector("#provider_phone").value.trim(),
            created_at: modal.querySelector("#provider_created_at").value.trim(),
            updated_at: getCurrentDateTime(), // Lấy ngày giờ hiện tại
        };

        return provider;
    }

    // Hàm call api thêm nhà cung cấp
    function callAPIAddProvider(providerData) {
        let apiProviderAdd = `${BASE_API_URL}/api/providers`;
        console.log("API thêm nhà cung cấp:", apiProviderAdd);
        $.ajax({
            url: apiProviderAdd,
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(providerData),
            success: function (response) {
                if (response.success) {
                    console.log("Thêm nhà cung cấp thành công!", response.data);
                    toast("Thêm nhà cung cấp thành công!", "success");
                } else {
                    console.error("Lỗi thêm nhà cung cấp:", response.message);
                    toast("Lỗi thêm nhà cung cấp!", "error");
                }
            },
            error: function (error) {
                console.error("Lỗi thêm nhà cung cấp:", error);
                console.log("Chi tiết lỗi:", error.responseText);
                toast("Lỗi thêm nhà cung cấp!", "error");
            }
        });
    }
});