<div class="container mt-5">
    <?php Flash::display(); ?>

    <h2>
        Available Rooms
        <?php if (!empty($arrival) && !empty($departure)): ?>
            from <?= htmlspecialchars($arrival) ?> to <?= htmlspecialchars($departure) ?> of <?= htmlspecialchars($guests) ?> person(s).
        <?php endif; ?>
    </h2>

    <!-- Filter Form -->
    <form method="GET" action="<?= APP_URL ?>/rooms">
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="price_range">Price Range</label>
                <select id="price_range" name="price_range" class="form-control">
                    <option value="">Select Price Range</option>
                    <option value="0-100" <?= (isset($_GET['price_range']) && $_GET['price_range'] === '0-100') ? 'selected' : '' ?>>RM 0 - RM 100</option>
                    <option value="101-200" <?= (isset($_GET['price_range']) && $_GET['price_range'] === '101-200') ? 'selected' : '' ?>>RM 101 - RM 200</option>
                    <option value="201-300" <?= (isset($_GET['price_range']) && $_GET['price_range'] === '201-300') ? 'selected' : '' ?>>RM 201 - RM 300</option>
                    <option value="300+" <?= (isset($_GET['price_range']) && $_GET['price_range'] === '300+') ? 'selected' : '' ?>>RM 300+</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="room_type">Room Type</label>
                <select id="room_type" name="room_type" class="form-control">
                    <option value="">Select Room Type</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['id'] ?>" <?= (isset($_GET['room_type']) && $_GET['room_type'] == $room['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($room['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <!-- Room Listings -->
    <form method="POST" action="<?= APP_URL ?>/booking-confirmation">
        <input type="hidden" name="arrival_date" value="<?= htmlspecialchars($arrival ?? '') ?>">
        <input type="hidden" name="departure_date" value="<?= htmlspecialchars($departure ?? '') ?>">
        <input type="hidden" name="guests" value="<?= htmlspecialchars($guests ?? 1) ?>">

        <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
                <?php 
                    $uploadDir = $base_url . '/uploads/rooms/';
                    $imagePath = !empty($room['image']) ? $uploadDir . $room['image'] : $uploadDir . 'default.jpg';
                ?>
                <div class="card mb-4 shadow-sm">
                    <div class="row g-0">
                        <!-- Left Column: Standardized Image -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center" style="height: 250px; overflow: hidden;">
                                <img src="<?= htmlspecialchars($imagePath) ?>" class="img-fluid"
                                     alt="<?= htmlspecialchars($room['name'] ?? 'Room') ?>"
                                     style="object-fit: cover; height: 100%; width: 100%;">
                            </div>
                        </div>

                        <!-- Middle Column: Room Details -->
                        <div class="col-md-5">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($room['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($room['description']) ?></p>
                                <div class="mb-2">
                                    <span class="badge rounded-pill bg-light text-dark border">
                                        <i class="fas fa-users text-primary me-1"></i> 
                                        Max Guests: <?= htmlspecialchars($room['capacity']) ?>
                                    </span>
                                </div>
                                <p><strong>Availability:</strong>
                                    <?php if ($room['available'] === null): ?>
                                        <span class="text-info">Select dates to see availability</span>
                                    <?php elseif ($room['available'] > 0): ?>
                                        <?= $room['available'] ?> rooms available
                                    <?php else: ?>
                                        <span class="text-danger">Room Full</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Right Column: Sticky Price & Actions -->
                        <div class="col-md-3">
                            <div class="position-sticky" style="top: 20px;">
                                <div class="d-flex flex-column justify-content-center align-items-center border-start p-3 h-100">
                                    <?php if (isset($room['is_dynamic']) && $room['is_dynamic']): ?>
                                        <p class="text-muted mb-0"><del>RM <?= number_format($room['price'], 2) ?></del></p>
                                        <p class="fs-4 fw-bold text-danger mb-0">RM <?= number_format($room['display_price'], 2) ?>/night</p>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-calendar-day"></i> Special Rate Applied</span>
                                    <?php else: ?>
                                        <p class="fs-4 fw-bold">RM <?= number_format($room['price'], 2) ?>/night</p>
                                    <?php endif; ?>
                                    <?php 
                                    $displayRating = ($room['total_reviews'] > 0) ? number_format($room['avg_rating'], 1) : 'New';
                                    ?>
                                    <p class="mb-4">
                                        <a href="javascript:void(0)" 
                                        class="text-decoration-none view-reviews" 
                                        data-room-id="<?= $room['id'] ?>" 
                                        data-room-name="<?= htmlspecialchars($room['name']) ?>">
                                        ⭐ <?= $displayRating ?> (<?= $room['total_reviews'] ?> Reviews)
                                        </a>
                                    </p>

                                    <?php if ($room['available'] === null): ?>
                                        <button type="button" class="btn btn-primary see-availability" 
                                                data-toggle="modal" 
                                                data-target="#availabilityModal"
                                                data-room="<?= htmlspecialchars($room['name']) ?>">
                                            See Availability
                                        </button>
                                    <?php elseif ($room['available'] > 0): ?>
                                        <label for="rooms[<?= $room['id'] ?>]" class="form-label">Quantity</label>
                                        <input type="number" name="rooms[<?= $room['id'] ?>]" min="0"
                                               max="<?= $room['available'] ?? 10 ?>" value="0"
                                               class="form-control mb-2" />
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>Room Full</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-warning">No rooms found with the selected filters.</p>
        <?php endif; ?>

        <div class="d-flex justify-content-end">
            <button id="nextBtn" type="submit" class="btn btn-primary mt-2" style="display:none;">
                Next
            </button>
        </div>
    </form>
</div>

<!-- 🔹 Bootstrap Modal for Date Selection -->
<div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white" id="availabilityModalLabel">Check Availability</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="availabilityForm" method="GET" action="<?= APP_URL ?>/rooms" class="modal-body">
        <div id="dateAlert" class="alert alert-danger d-none"></div>
        <div class="mb-3">
          <label for="arrival_date" class="form-label">Arrival Date</label>
          <input type="date" id="arrival_date" name="arrival_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="departure_date" class="form-label">Departure Date</label>
          <input type="date" id="departure_date" name="departure_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="guests" class="form-label">Total Guests</label>
          <input type="number" id="guests" name="guests" class="form-control" min="1" value="1" required>
        </div>
        <div class="d-flex justify-content-end mt-3">
          <button type="submit" class="btn btn-danger">Check</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Review -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white" id="reviewModalLabel">Room Reviews</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
        <div id="reviewsContainer">
            </div>
        
        <div id="reviewFormContainer" class="d-none mt-4 p-3 border rounded bg-light">
            <h6 class="fw-bold">Write a Review</h6>
            <form action="<?= APP_URL ?>/submit-review" method="POST">
                <input type="hidden" name="room_id" id="review_room_id">
                <div class="mb-2">
                    <label class="small fw-semibold text-muted mb-1">Rating</label>
                    <select name="rating" class="form-select border-0 shadow-sm" required>
                        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                        <option value="4">⭐⭐⭐⭐ Very Good</option>
                        <option value="3">⭐⭐⭐ Average</option>
                        <option value="2">⭐⭐ Poor</option>
                        <option value="1">⭐ Terrible</option>
                    </select>
                </div>
                <div class="mb-2">
                    <textarea name="comment" class="form-control border-0 shadow-sm" placeholder="Tell us about your stay..." rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm py-2">Submit Review</button>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Thank You Message -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <?php if ($flash = Flash::get('success')): ?>
    <div id="successToast" class="toast show align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="fas fa-check-circle me-2"></i> <?= $flash ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($flash = Flash::get('error')): ?>
    <div id="errorToast" class="toast show align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="fas fa-exclamation-triangle me-2"></i> <?= $flash ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- 🔹 JavaScript: Autofill + Date Validation -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Existing Elements ---
    const modal = document.getElementById('availabilityModal');
    const form = document.getElementById('availabilityForm');
    const alertBox = document.getElementById('dateAlert');
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');
    const guestInput = document.getElementById('guests');
    const quantityInputs = document.querySelectorAll('input[name^="rooms["]');
    const nextBtn = document.getElementById('nextBtn');

    // --- Review Specific Elements ---
    const reviewsModal = new bootstrap.Modal(document.getElementById('reviewsModal'));
    const reviewsContainer = document.getElementById('reviewsContainer');
    const reviewFormContainer = document.getElementById('reviewFormContainer');
    const reviewModalTitle = document.getElementById('reviewModalLabel');

    /* ============================================================
       DATE HANDLING & AVAILABILITY MODAL
    ============================================================ */
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    if (arrivalInput && departureInput) {
        arrivalInput.value = arrivalInput.value || formatDate(today);
        departureInput.value = departureInput.value || formatDate(tomorrow);
        arrivalInput.min = formatDate(today);
        departureInput.min = formatDate(tomorrow);

        arrivalInput.addEventListener('change', () => {
            const arrivalDate = new Date(arrivalInput.value);
            const nextDay = new Date(arrivalDate);
            nextDay.setDate(arrivalDate.getDate() + 1);
            departureInput.min = formatDate(nextDay);
            if (new Date(departureInput.value) <= arrivalDate) {
                departureInput.value = formatDate(nextDay);
            }
        });
    }

    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const roomName = button.getAttribute('data-room');
            const modalTitle = modal.querySelector('.modal-title');
            modalTitle.textContent = `Check Availability – ${roomName}`;
            alertBox.classList.add('d-none');
        });
    }

    if (form) {
        form.addEventListener('submit', event => {
            const arrival = new Date(arrivalInput.value);
            const departure = new Date(departureInput.value);
            if (isNaN(arrival) || isNaN(departure) || departure <= arrival) {
                alertBox.textContent = "Please ensure departure is after arrival.";
                alertBox.classList.remove('d-none');
                event.preventDefault();
            }
        });
    }

    /* ============================================================
       NEXT BUTTON VISIBILITY
    ============================================================ */
    function toggleNextButton() {
        let show = false;
        quantityInputs.forEach(input => {
            if (parseInt(input.value) > 0) show = true;
        });
        if (nextBtn) nextBtn.style.display = show ? 'block' : 'none';
    }

    quantityInputs.forEach(input => {
        input.addEventListener('input', toggleNextButton);
    });
    toggleNextButton();

    /* ============================================================
       NEW: REVIEW POPUP LOGIC (AJAX)
    ============================================================ */
    document.querySelectorAll('.view-reviews').forEach(link => {
        link.addEventListener('click', function() {
            const roomId = this.getAttribute('data-room-id');
            const roomName = this.getAttribute('data-room-name');
            
            reviewModalTitle.textContent = `Reviews for ${roomName}`;
            reviewsContainer.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div><p>Loading reviews...</p></div>';
            
            reviewsModal.show();

            // Fetch from your controller (ensure this route exists in your routes)
            fetch(`<?= APP_URL ?>/get-reviews/${roomId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.reviews.length === 0) {
                        reviewsContainer.innerHTML = '<div class="alert alert-light border text-center">No reviews yet for this room.</div>';
                    } else {
                        let html = '<div class="list-group list-group-flush">';
                        data.reviews.forEach(r => {
                            const initial = r.full_name.charAt(0).toUpperCase();
                            const stars = '⭐'.repeat(r.rating);
                            html += `
                                <div class="review-card mb-3 p-3 rounded-3 border-bottom shadow-xs">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-circle me-3">${initial}</div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold small">${r.full_name}</h6>
                                            <div class="text-warning" style="font-size: 0.85rem;">
                                                ${stars} <span class="text-muted small ms-1">${r.rating}/5</span>
                                            </div>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.7rem;">
                                            ${new Date(r.created_at).toLocaleDateString('en-GB')}
                                        </div>
                                    </div>
                                    <p class="mb-0 text-dark small lh-sm italic" style="font-style: italic;">
                                        "${r.comment}"
                                    </p>
                                </div>`;
                        });
                        html += '</div>';
                        reviewsContainer.innerHTML = html;
                    }

                    // Only show review form if controller says they are eligible
                    if (data.canReview) {
                        reviewFormContainer.classList.remove('d-none');
                        // Set the room_id in a hidden field inside your review form if needed
                        const hiddenRoomInput = reviewFormContainer.querySelector('#review_room_id');
                        if (hiddenRoomInput) hiddenRoomInput.value = roomId;
                    } else {
                        reviewFormContainer.classList.add('d-none');
                    }
                })
                .catch(err => {
                    reviewsContainer.innerHTML = '<div class="alert alert-danger">Error loading reviews.</div>';
                });
        });
    });

    // THANK YOU MESSAGE
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toastEl => {
        // Auto-hide the toast after 5 seconds
        setTimeout(() => {
            const bsToast = bootstrap.Toast.getInstance(toastEl);
            if (bsToast) bsToast.hide();
        }, 5000);
    });
});
</script>
