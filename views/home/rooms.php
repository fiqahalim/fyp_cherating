<div class="container mt-5">
    <?php Flash::display(); ?>

    <h2>
        Available Rooms
        <?php if (!empty($arrival) && !empty($departure)): ?>
            from <?= htmlspecialchars($arrival) ?> to <?= htmlspecialchars($departure) ?>
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
                                <p class="text-muted mb-1">Capacity: <?= htmlspecialchars($room['capacity']) ?> guests</p>
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
                                    <p class="fs-4 fw-bold">RM <?= number_format($room['price'], 2) ?>/night</p>
                                    <p class="mb-1">⭐ 4.5 Review</p>
                                    <p class="mb-3">Additional Fee: RM 20</p>

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
      <div class="modal-header">
        <h5 class="modal-title" id="availabilityModalLabel">Check Availability</h5>
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
        <div class="d-flex justify-content-end mt-3">
          <button type="submit" class="btn btn-success">Check</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 🔹 JavaScript: Autofill + Date Validation -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('availabilityModal');
    const form = document.getElementById('availabilityForm');
    const alertBox = document.getElementById('dateAlert');
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');

    /* ============================================================
       DATE HANDLING & MODAL LOGIC (your original script)
    ============================================================ */

    // Helper: format date to YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Autofill today's and tomorrow's dates
    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    arrivalInput.value = formatDate(today);
    departureInput.value = formatDate(tomorrow);

    // Prevent selecting past dates
    arrivalInput.min = formatDate(today);
    departureInput.min = formatDate(tomorrow);

    // Update departure min when arrival changes
    arrivalInput.addEventListener('change', () => {
        const arrivalDate = new Date(arrivalInput.value);
        const nextDay = new Date(arrivalDate);
        nextDay.setDate(arrivalDate.getDate() + 1);
        departureInput.min = formatDate(nextDay);
        if (new Date(departureInput.value) <= arrivalDate) {
            departureInput.value = formatDate(nextDay);
        }
    });

    // Update modal title dynamically based on clicked room
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const roomName = button.getAttribute('data-room');
        const modalTitle = modal.querySelector('.modal-title');
        modalTitle.textContent = `Check Availability – ${roomName}`;
        alertBox.classList.add('d-none');
    });

    // Validate date logic before form submission
    form.addEventListener('submit', event => {
        const arrival = new Date(arrivalInput.value);
        const departure = new Date(departureInput.value);

        if (isNaN(arrival) || isNaN(departure)) {
            alertBox.textContent = "Please select both arrival and departure dates.";
            alertBox.classList.remove('d-none');
            event.preventDefault();
            return;
        }

        if (departure <= arrival) {
            alertBox.textContent = "Departure date must be after arrival date.";
            alertBox.classList.remove('d-none');
            event.preventDefault();
            return;
        }

        alertBox.classList.add('d-none'); // hide alert if good
    });

    /* ============================================================
       NEW FEATURE: Show "Next" button ONLY when qty > 0
    ============================================================ */

    const quantityInputs = document.querySelectorAll('input[name^="rooms["]');
    const nextBtn = document.getElementById('nextBtn'); // <-- make sure button has this id

    function toggleNextButton() {
        let show = false;

        quantityInputs.forEach(input => {
            if (parseInt(input.value) > 0) {
                show = true;
            }
        });

        nextBtn.style.display = show ? 'block' : 'none';
    }

    // Listen for changes on all quantity inputs
    quantityInputs.forEach(input => {
        input.addEventListener('input', toggleNextButton);
    });

    // Run once on page load
    toggleNextButton();
});
</script>
