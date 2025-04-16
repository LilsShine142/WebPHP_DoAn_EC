<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .limiter-menu-desktop, .content-topbar{
            padding: 0px 178px;
        }
    </style>
</head>
<body class="bg-gray-100 p-5">
    <div style="width: 80%; margin: auto; margin-bottom: 40px;">
        <!-- Top Navigation -->
        <div class="flex flex-wrap justify-between bg-white p-4 shadow-md rounded-lg mb-4" style="margin-top: 40px;">
            <div class="w-full mb-2 flex justify-center">
                <button class="filter-btn px-4 py-2 bg-blue-500 text-white" data-status="all">All</button>
                <button class="filter-btn px-4 py-2" data-status="1">Pending</button>
                <button class="filter-btn px-4 py-2" data-status="2">Approve</button>
                <button class="filter-btn px-4 py-2" data-status="4">To Ship</button>
                <button class="filter-btn px-4 py-2" data-status="5">Received</button>
                <button class="filter-btn px-4 py-2" data-status="3">Canceled</button>
            </div>
            <div class="w-full flex justify-center">
                <input type="text" id="searchInput" class="w-1/3 p-2 border rounded-md" placeholder="You can search by Order ID or Product name">
            </div>
        </div>

        
        <!-- Danh sách đơn hàng -->
        <div id="ordersList" class="space-y-4">
            <!-- Mỗi đơn hàng -->
            
        </div>

        <!-- Rating Modal -->
        <div id="ratingModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">    
            <div class="modal-content bg-white p-5 rounded-lg shadow-lg" style="width: 40%;">
                <h2 class="text-lg font-bold mb-4">Rate Product</h2>
                <div id="ratingProductDetails" class="mb-4"></div>
                <div class="flex mb-4">
                    <span class="mr-2">Rating:</span>
                    <div id="starRating" class="flex"></div>
                </div>
                <textarea id="ratingComment" rows="3" class="w-full border rounded-md p-2" placeholder="Leave your comment..."></textarea>
                <div class="mt-4 flex justify-end">
                    <button id="submitRating" class="px-4 py-2 bg-green-500 text-white rounded-md">Rate</button>
                    <button id="closeModal" class="px-4 py-2 bg-red-500 text-white rounded-md ml-2">Close</button>
                </div>
            </div>
        </div>

        <script>
            document.querySelectorAll('.filter-btn').forEach(button => {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('bg-blue-500', 'text-white'));
                    this.classList.add('bg-blue-500', 'text-white');
                    filterOrders(this.getAttribute('data-status'));
                });
            });
            
            function filterOrders(status) {
                document.querySelectorAll('.order').forEach(order => {
                    order.style.display = (status === 'all' || order.getAttribute('data-status') === status) ? 'block' : 'none';
                });
            }
            
            document.getElementById('searchInput').addEventListener('input', function () {
                let keyword = this.value.toLowerCase();
                document.querySelectorAll('.order').forEach(order => {
                    let orderId = order.querySelector('h2').textContent.toLowerCase();
                    let productName = order.querySelector('.font-semibold').textContent.toLowerCase();
                    order.style.display = (orderId.includes(keyword) || productName.includes(keyword)) ? 'block' : 'none';
                });
            });
        </script>
    </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function formatCurrency(amount) {
        return new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" }).format(amount);
    }

    $(document).ready(function() {
        const userData = localStorage.getItem("user");
        if (userData) {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;

            $.ajax({
                url: `${BASE_API_URL}/api/orders?user_id=${user_id}`,
                type: "GET",
                success: function(response) {
                    $("#ordersList").empty();
                    response.data.sort((a, b) => new Date(b.order_date) - new Date(a.order_date));

                    response.data.forEach(order => {
                        const orderElement = $(`
                            <div class="order bg-white p-4 rounded-lg shadow-md" data-status="${order.delivery_state_id}">
                                <h2 class="font-bold text-lg">Order ID: #${order.id}</h2>
                                <p class="text-lg">Status: ${order.delivery_state_id == 1 ? "Pending" : order.delivery_state_id == 2 ? "Approved" : order.delivery_state_id == 3 ? "Canceled" : order.delivery_state_id == 4 ? "To Ship" : "Received"}</p>
                                <div class="grid grid-cols-1 gap-3 mt-3" id="orderItems"></div>
                                <div class="mt-3 text-right font-bold text-lg">Total: ${formatCurrency(order.total_cents)}</div>
                                <div class="mt-3 text-right">
                                    ${order.payment_method == "COD" && (order.delivery_state_id == 1 || order.delivery_state_id == 2) ? `<button class="px-4 py-2 bg-red-500 text-white rounded-md" onclick="cancelOrder(${order.id})">Cancel</button>` : ''}

                                    <button class="px-4 py-2 bg-blue-500 text-white rounded-md">
                                        <a href="index.php?content=pages/order-detail.php&id=${order.id}">View Details</a>
                                    </button>
                                </div>
                            </div>
                        `);

                        $("#ordersList").append(orderElement);

                        window.cancelOrder = function(orderId) {
                            if (confirm("Are you sure you want to cancel this order?")) {
                                $.ajax({
                                    url: `${BASE_API_URL}/api/orders/${orderId}`,
                                    type: 'PUT',
                                    contentType: 'application/json',
                                    data: JSON.stringify({
                                        delivery_state_id: 3 // Cập nhật trạng thái giao hàng thành "Cancelled"
                                    }),
                                    success: function() {
                                        alert("Order cancelled successfully!");
                                        location.reload(); // Reload the page to see updated order status
                                    },
                                    error: function() {
                                        alert("Failed to cancel the order.");
                                    }
                                });
                            }
                        };

                        $.ajax({
                            url: `${BASE_API_URL}/api/orders/${order.id}`,
                            type: "GET",
                            success: function(response) {
                                response.data.forEach(variant => {
                                    const orderItems = $(`
                                        <div class="order-product-container flex items-center p-3 border rounded-lg" style="position: relative;">
                                            <img src="../backend/uploads/products/${variant.image_name}" alt="${variant.product_variation_id}" class="w-16 h-16 object-cover rounded-md">
                                            <div class="ml-3">   
                                                <p class="font-semibold">${variant.name}</p>
                                                <p class="text-sm text-gray-600">Viriation: ${variant.watch_color} - ${variant.watch_size_mm} mm</p>
                                                <p class="text-sm">Quantity: ${variant.quantity}</p>
                                                <p class="text-sm font-bold">Price: ${formatCurrency(variant.price_cents)}</p>
                                            </div>
                                        </div>
                                    `);
                                    orderElement.find("#orderItems").append(orderItems);
                                    
                                    if (variant.delivery_state_id == 5) {
                                        const receivedDate = new Date(variant.received_date);
                                        const oneWeekAgo = new Date();
                                        oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);   

                                        if (receivedDate >= oneWeekAgo) {
                                            $.ajax({
                                                url: `${BASE_API_URL}/api/feedbacks?product_variation_id=${variant.product_variation_id}&user_id=${user_id}`,
                                                type: "GET",
                                                success: function(feedbackResponse) {
                                                    if (feedbackResponse.length == 0) {
                                                        const rateButton = $(`
                                                            <button class="rate-button px-4 py-2 bg-green-500 text-white rounded-md mr-2" style="position: absolute; right: 32px;">
                                                                Rate
                                                            </button>
                                                        `);
                                                        orderItems.append(rateButton);
                                                    } else {
                                                        const rateButton = $(`
                                                            <button class="edit-rate-button px-4 py-2 bg-yellow-500 text-white rounded-md mr-2" style="position: absolute; right: 32px;" data-variant='${JSON.stringify(variant)}'>
                                                                Edit Rate
                                                            </button>
                                                        `);
                                                        orderItems.append(rateButton);
                                                    }
                                                },
                                                error: function() {
                                                    console.log("Có lỗi xảy ra khi kiểm tra feedback.");
                                                }
                                            });
                                        }
                                    }
                                });
                            },
                            error: function() {
                                console.log("Có lỗi xảy ra khi lấy thông tin đơn hàng.");
                            }
                        });
                    });
                },
                error: function() {
                    console.log("Có lỗi xảy ra khi lấy danh sách đơn hàng.");
                }
            });
        } else {
            console.log("Không tìm thấy dữ liệu user trong localStorage.");
        }

        // Handle Rate button click
        $(document).on('click', '.rate-button', function() {
            const variant = $(this).closest('.order-product-container').find('img').attr('src');
            const variantName = $(this).closest('.order-product-container').find('.font-semibold').text();
            const variantColor = $(this).closest('.order-product-container').find('.text-sm').eq(0).text().split(': ')[1];
            const variantId = $(this).closest('.order-product-container').find('img').attr('alt');
            populateRatingModal(variant, variantName, variantColor, variantId); 
        });

        // Handle Edit Rate button click
        $(document).on('click', '.edit-rate-button', function() {
            const variant = $(this).data('variant');
            populateRatingModal("../backend/uploads/products/" + variant.image_name, variant.name, variant.watch_color, variant.product_variation_id, true);
        });

        // Populate modal with product details and stars
        function populateRatingModal(variantImg, variantName, variantColor, variantId, isEdit = false) {
            const productDetails = `
                <img src="${variantImg}" alt="${variantId}" class="w-16 h-16 object-cover rounded-md">
                <p class="font-semibold">${variantName}</p>
                <p class="text-sm">Color: ${variantColor}</p>
            `;
            $('#ratingProductDetails').html(productDetails);

            // Add star rating UI
            $('#starRating').empty();
            for (let i = 1; i <= 5; i++) {
                $('#starRating').append(`
                    <svg class="star w-6 h-6 cursor-pointer" data-value="${i}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="9.9,1.1 12,7.2 18.2,7.2 13.6,11.5 15.5,17.8 9.9,13.5 4.3,17.8 6.2,11.5 1.6,7.2 7.8,7.2"></polygon>
                    </svg>
                `);
            }

            if (isEdit) {
                const userObject = JSON.parse(userData);
                const user_id = userObject.id;
                // Fetch existing rating and comment
                $.ajax({
                    url: `${BASE_API_URL}/api/feedbacks?product_variation_id=${variantId}&user_id=${user_id}`,
                    type: "GET",
                    success: function(response) {
                        if (response.length > 0) {
                            const feedback = response.data[0];
                            $('#submitRating').data('feedbackId', feedback.id); // Store feedback ID for update
                            $('#ratingComment').val(feedback.content);
                            $('#submitRating').data('rating', feedback.rating); // Store existing rating value
                            $('#starRating .star').each(function(index) {
                                $(this).attr('fill', index < feedback.rating ? 'yellow' : 'none');
                            });
                        }
                    },
                    error: function() {
                        console.error("Error fetching existing rating.");
                    }
                });
                $('#submitRating').text('Update Rating');
            } else {
                $('#submitRating').text('Rate');
            }

            // Open the modal
            $('#ratingModal').removeClass('hidden');

            // Star rating click event
            $('.star').on('click', function() {
                const ratingValue = $(this).data('value');
                $('#starRating .star').each(function(index) {
                    $(this).attr('fill', index < ratingValue ? 'yellow' : 'none');
                });
                $('#submitRating').data('rating', ratingValue); // Store selected rating value
            });
        }

        // Submit rating
        $('#submitRating').on('click', function() {
            const userObject = JSON.parse(userData);
            const user_id = userObject.id;
            const selectedRating = $(this).data('rating');
            const comment = $('#ratingComment').val();
            const product_variation_id = $(this).closest('.modal-content').find('img').attr('alt');

            if (selectedRating && comment) {
                const feedbackId = $(this).data('feedbackId');
                if(feedbackId) {
                    // Update existing feedback
                    $.ajax({
                        url: `${BASE_API_URL}/api/feedbacks`,
                        type: "PATCH",
                        data: JSON.stringify({
                            id: feedbackId,
                            user_id: user_id,
                            product_variation_id: product_variation_id,
                            content: comment,
                            rating: selectedRating
                        }),
                        contentType: "application/json",
                        success: function() {
                            alert('Feedback updated successfully!');
                            $('#ratingModal').addClass('hidden');
                            location.reload(); // Reload the page to see updated feedback
                        },
                        error: function() {
                            alert('Error updating feedback.');
                        }
                    });
                } else {
                    // Create new feedback
                    $.ajax({
                        url: `${BASE_API_URL}/api/feedbacks`,
                        type: "POST",
                        data: JSON.stringify({
                            user_id: user_id,
                            product_variation_id: product_variation_id,
                            content: comment,
                            rating: selectedRating
                        }),
                        contentType: "application/json",
                        success: function() {
                            alert('Feedback submitted successfully!');
                            $('#ratingModal').addClass('hidden');
                            location.reload(); // Reload the page to see new feedback
                        },
                        error: function() {
                            alert('Error submitting feedback.');
                        }
                    });
                }
            } else {
                alert('Please select a rating and add a comment.');
            }
        });

        // Close modal
        $('#closeModal').on('click', function() {
            $('#ratingModal').addClass('hidden');
        });
    });
</script>
