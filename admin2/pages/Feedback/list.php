<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Feedback List</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th style="width: 10%;">User ID</th>
                        <th style="width: 10%;">Product Variation ID</th>
                        <th style="width: 10%;">Product Image</th>
                        <th>Content</th>
                        <th style="width: 6%;">Rating</th>
                        <th style="width: 10%;">Created At</th>
                        <th style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="feedback-data-table">
                    <!-- Data will be inserted here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Responding to Feedback -->
    <div class="modal fade" id="responseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel">Respond to Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="existing-responses" class="mb-3">
                        <!-- Existing responses will be populated here -->
                    </div>
                    <form id="responseForm">
                        <input type="hidden" id="feedbackId" name="feedbackId">
                        <div class="form-group">
                            <label for="response_content">Response Content</label>
                            <textarea class="form-control" id="response_content" name="response_content" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitResponse">Submit Response</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fetchFeedback() {
            $.ajax({
                url: `${BASE_API_URL}/api/feedbacks`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let tableBody = document.getElementById('feedback-data-table');
                        let html = '';

                        // Create an array to hold promises for fetching product images
                        let imageFetchPromises = response.data.map(feedback => {
                            return $.ajax({
                                url: `${BASE_API_URL}/api/products/variations/${feedback.product_variation_id}`,
                                type: 'GET',
                                dataType: 'json'
                            }).then(imageResponse => {
                                return {
                                    id: feedback.id,
                                    user_id: feedback.user_id,
                                    product_variation_id: feedback.product_variation_id,
                                    content: feedback.content,
                                    rating: feedback.rating,
                                    created_at: feedback.created_at,
                                    image_name: imageResponse.success ? imageResponse.data.image_name : null // Get image name
                                };
                            });
                        });

                        // Wait for all image fetches to complete
                        Promise.all(imageFetchPromises).then(feedbacksWithImages => {
                            feedbacksWithImages.forEach(feedback => {
                                html += `
                                <tr id="feedback-${feedback.id}">
                                    <td>${feedback.id}</td>
                                    <td>${feedback.user_id}</td>
                                    <td>${feedback.product_variation_id}</td>
                                    <td>
                                        ${feedback.image_name ? `<img src="../backend/uploads/products/${feedback.image_name}" alt="Product Image" style="width: 50px; height: auto;">` : 'No Image'}
                                    </td>
                                    <td>${feedback.content}</td>
                                    <td>${feedback.rating}</td>
                                    <td>${new Date(feedback.created_at).toLocaleString()}</td>
                                    <td>
                                        <button class="btn btn-warning btn-response" data-id="${feedback.id}" data-bs-toggle="modal" data-bs-target="#responseModal">
                                            Respond
                                        </button>
                                    </td>
                                </tr>`;
                            });

                            tableBody.innerHTML = html;

                            // Attach event listeners to response buttons
                            document.querySelectorAll('.btn-response').forEach(button => {
                                button.addEventListener('click', function() {
                                    const feedbackId = this.getAttribute('data-id');
                                    document.getElementById('feedbackId').value = feedbackId;
                                    displayExistingResponses(feedbackId); // Fetch existing responses
                                });
                            });
                        }).catch(() => {
                            console.error("Error fetching product images.");
                        });
                    } else {
                        console.warn("No feedback available.");
                    }
                },
                error: function() {
                    console.error("Error loading feedback.");
                }
            });
        }

        function displayExistingResponses(feedbackId) {
            $.ajax({
                url: `${BASE_API_URL}/api/feedbacks/responses?feedback_id=${feedbackId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let responsesDiv = document.getElementById('existing-responses');
                        responsesDiv.innerHTML = ''; // Clear previous responses

                        response.data.forEach(responseItem => {
                            responsesDiv.innerHTML += `
                                <div class="response-item mb-2 p-2 border rounded">
                                    <strong>Admin:</strong> ${responseItem.response_content}
                                    <span class="text-muted ms-2">${new Date(responseItem.created_at).toLocaleString()}</span>
                                </div>`;
                        });
                    } else {
                        document.getElementById('existing-responses').innerHTML = '<p class="text-muted">No responses yet.</p>';
                    }
                },
                error: function() {
                    console.error("Error loading existing responses.");
                }
            });
        }

        document.getElementById('submitResponse').addEventListener('click', function() {
            const feedbackId = document.getElementById('feedbackId').value;
            const responseContent = document.getElementById('response_content').value;

            $.ajax({
                url: `${BASE_API_URL}/api/feedbacks/responses`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    feedback_id: feedbackId,
                    response_content: responseContent,
                    admin_id: 1 // Assuming admin ID is 1 for this example
                }),
                success: function(response) {
                    if (response.success) {
                        Toastify({
                            text: "Response submitted successfully!",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "green",
                        }).showToast();
                        fetchFeedback(); // Refresh the feedback list
                        $('#responseModal').modal('hide'); // Close the modal
                    } else {
                        Toastify({
                            text: "Failed to submit response.",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "red",
                        }).showToast();
                    }
                },
                error: function() {
                    console.error("Error submitting response.");
                }
            });
        });

        // Fetch feedback on page load
        $(document).ready(function() {
            fetchFeedback();
        });
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>