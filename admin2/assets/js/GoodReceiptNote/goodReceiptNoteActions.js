document.addEventListener("DOMContentLoaded", function () {
    // =========================== LẤY DANH SÁCH GOOD RECEIPT NOTE ===========================
    // CALL API LẤY DANH SÁCH GOOD RECEIPT NOTE
    $(document).ready(function () {
        console.log("GOOD RECEIPT NOTE");
        getAPIGoodReceiptNotes();
    });
    // Hàm format tiền Việt 
    function formatCurrencyVND(amount) {
        if (isNaN(amount)) {
            console.error("Invalid amount:", amount);
            return "0 ₫";
        }
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }

    function getAPIGoodReceiptNotes(receiptId) {
        let goodReceiptNoteAPIURL = receiptId ? `${BASE_API_URL}/api/goods_receipt_notes/${receiptId}` : `${BASE_API_URL}/api/goods_receipt_notes`;
        console.log("RECEIPT ID", receiptId);
        $.ajax({
            url: goodReceiptNoteAPIURL,
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const goodReceiptNodeData = response.data;
                    console.log("goodReceiptNodeData DATA ", goodReceiptNodeData);
                    let providerId = goodReceiptNodeData[0].provider_id;
                    let staffId = goodReceiptNodeData[0].staff_id;
                    getProviderAndStaffData(goodReceiptNodeData, providerId, staffId);
                }
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                alert("Error: " + error.message);
            }
        })
    }

    //Call api lấy tên của nhà cung cấp và tên của nhân viên để render trong good receipt note
    async function getProviderAndStaffData(goodReceiptNodeData, providerId, staffId) {

        let providerAPIURL = `${BASE_API_URL}/api/providers/${providerId}`;
        let staffAPIURL = `${BASE_API_URL}/api/users/${staffId}`;
        console.log("PROVIDER URL", providerAPIURL);
        console.log("STAFF URL", staffAPIURL);
        if (!providerId || !staffId) {
            console.error("Không có dữ liệu nhà cung cấp hoặc nhân viên");
            return;
        }
        // Gọi API song song 
        Promise.all([
            $.ajax({ url: providerAPIURL, type: "GET", dataType: "json" }),
            $.ajax({ url: staffAPIURL, type: "GET", dataType: "json" }),
        ]).then(([providerResponse, staffResponse]) => {
            if (providerResponse.success && staffResponse.success) {
                let provider = providerResponse.data;
                let staff = staffResponse.data;
                console.log("Provider", provider);
                console.log("Staff", staff);

                // Gộp dữ liệu vào good Receipt Node
                let mergedgoodReceiptNodeDatas = goodReceiptNodeData.map(receipt => ({
                    ...receipt,
                    provider_name: provider.full_name || "Không xác định",
                    staff_name: staff.full_name || "Không xác định",
                }));

                console.log("Merged mergedgoodReceiptNodeDatas:", mergedgoodReceiptNodeDatas);
                renderGooReceiptNoteToTable(mergedgoodReceiptNodeDatas);
            } else {
                console.error("Lỗi khi tải dữ liệu good receipt note, provider hoặc staff");
            }
        })
            .catch(error => {
                console.error("Lỗi API:", error);
            });
    }

    // RENDER DỮ LIỆU RA BẢNG
    function renderGooReceiptNoteToTable(goodReceiptNodeDatas) {
        let goodReceiptNoteTable = document.getElementById("good_receipt_note-table");
        console.log("goodReceiptNoteTable", goodReceiptNoteTable);
        goodReceiptNoteTable.innerHTML = "";
        console.log("goodReceiptNodeDatas", goodReceiptNodeDatas);
        goodReceiptNodeDatas.forEach((receipt, index) => {
            let total_price_cents = formatCurrencyVND(receipt.total_price_cents);
            console.log("format tiền", total_price_cents);
            goodReceiptNoteTable.innerHTML += `
        <tr>
            <td>${index + 1}</td>
                <td>${receipt.id}</td>
                <td>${receipt.name}</td>
                <td>${receipt.provider_name}</td>
                <td>${receipt.staff_name}</td>
                <td>${total_price_cents}</td>
                <td>${receipt.quantity}</td>
                <td>${receipt.created_at}</td>
                <td>
                    <button class="btn btn-info btn-view" data-id="${receipt.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-update" data-id="${receipt.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-delete" data-id="${receipt.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
        </tr>
        `;
        });
    }






});