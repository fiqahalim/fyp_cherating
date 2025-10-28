<div class="container mt-5 mb-5 p-4 shadow rounded bg-white" style="max-width: 800px;">
    <div class="text-center">
        <img src="<?= APP_URL ?>/assets/images/checkmark.png" alt="Confirmed" width="80" class="mb-3">
        <h2 class="text-success">Booking Confirmed!</h2>
        <p><strong>Booking Reference:</strong> <?= htmlspecialchars($booking['booking_ref_no']) ?></p>
        <p class="lead">Thank you, <strong><?= htmlspecialchars($booking['full_name']) ?></strong>! Your booking has been successfully confirmed.</p>
    </div>

    <hr>

    <div class="row mt-4">
        <div class="col-md-6">
            <h3 class="text-primary">Booking Details</h3>
            <ul class="list-unstyled">
                <li><strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in']) ?></li>
                <li><strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out']) ?></li>
                <li><strong>Status:</strong> <?= ucfirst($booking['status']) ?></li>
                <li><strong>Payment Status:</strong> <?= ucfirst($booking['payment_status']) ?></li>
                <li><strong>Payment Method:</strong> <?= ucfirst($booking['payment_method']) ?></li>
                <li><strong>Total Amount:</strong> RM <?= number_format($booking['total_amount'], 2) ?></li>
            </ul>
        </div>
        <div class="col-md-6">
            <h3 class="text-primary">Contact Info</h3>
            <ul class="list-unstyled">
                <li><strong>Name:</strong> <?= htmlspecialchars($booking['full_name']) ?></li>
                <li><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></li>
                <li><strong>Phone:</strong> <?= htmlspecialchars($booking['phone']) ?></li>
            </ul>
        </div>
    </div>

    <hr>

    <h5 class="mt-4">Rooms Booked</h5>
    <ul class="list-group">
        <?php foreach ($booking['rooms'] as $room): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center text-primary">
                <?= htmlspecialchars($room['name']) ?>
                <span>Qty: <?= $room['rooms_booked'] ?> &nbsp;|&nbsp; RM <?= number_format($room['price'], 2) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="text-center mt-4">
        <a href="<?= APP_URL ?>/download-invoice/<?= $booking['id'] ?>" class="btn btn-outline-primary">Download Invoice</a>
        <a href="<?= APP_URL ?>" class="btn btn-secondary">Back to Home</a>
    </div>
</div>