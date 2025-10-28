<div class="container mt-5">
    <?php Flash::display(); ?>
    
    <h2>Available Rooms from <?= htmlspecialchars($arrival ?? '') ?> to <?= htmlspecialchars($departure ?? '') ?></h2>

    <!-- Filter Form -->
    <form method="GET" action="<?= APP_URL ?>/rooms">
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="price_range">Price Range</label>
                <select id="price_range" name="price_range" class="form-control">
                    <option value="">Select Price Range</option>
                    <option value="0-100" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '0-100') ? 'selected' : '' ?>>RM 0 - RM 100</option>
                    <option value="101-200" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '101-200') ? 'selected' : '' ?>>RM 101 - RM 200</option>
                    <option value="201-300" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '201-300') ? 'selected' : '' ?>>RM 201 - RM 300</option>
                    <option value="300+" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '300+') ? 'selected' : '' ?>>RM 300+</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="room_type">Room Type</label>
                <select id="room_type" name="room_type" class="form-control">
                    <option value="">Select Room Type</option>
                    <!-- Populate room types dynamically from $rooms -->
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['id'] ?>" <?= (isset($_GET['room_type']) && $_GET['room_type'] == $room['id']) ? 'selected' : '' ?>><?= htmlspecialchars($room['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <form method="POST" action="<?= APP_URL ?>/booking-confirmation">
        <input type="hidden" name="arrival_date" value="<?= htmlspecialchars($arrival) ?>">
        <input type="hidden" name="departure_date" value="<?= htmlspecialchars($departure) ?>">

        <!-- Room Listings -->
        <div class="row">
            <?php foreach ($rooms as $room): ?>
                <div class="col-12 mb-4">
                    <div class="card">
                        <!-- Image -->
                        <img src="<?= htmlspecialchars($room['image'] ?? 'path/to/default-image.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($room['name'] ?? 'Room') ?>">

                        <!-- Card Body -->
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($room['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($room['description']) ?></p>
                            <p class="text-muted">RM <?= number_format($room['price'], 2) ?> per night</p>

                            <p>
                                <strong>Availability: </strong>
                                <?php if ($room['available'] > 0): ?>
                                    <?= $room['available'] ?> rooms available
                                <?php else: ?>
                                    <span class="text-danger">Room Full</span>
                                <?php endif; ?>
                            </p>

                            <?php if ($room['available'] > 0): ?>
                                <label for="rooms[<?= $room['id'] ?>]" class="form-label">Quantity</label>
                                <input type="number" name="rooms[<?= $room['id'] ?>]" min="0" max="<?= $room['available'] ?>" value="0" class="form-control" />
                            <?php else: ?>
                                <input type="number" disabled class="form-control" />
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Next</button>
        <a href="<?= APP_URL ?>" class="btn btn-secondary mt-4">Cancel</a>
    </form>
</div>
