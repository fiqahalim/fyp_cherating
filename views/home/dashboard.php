<div class="container-fluid">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
    <p>Your booking history:</p>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">No bookings found.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($bookings as $booking): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="font-weight-bold">Booking #<?= $booking['id'] ?></h5>
                            <p>Status: <?= $booking['status'] ?></p>
                            <p>Check-in: <?= $booking['check_in'] ?></p>
                            <p>Check-out: <?= $booking['check_out'] ?></p>
                            <p>Rooms:</p>
                            <ul>
                                <?php foreach ($booking['rooms'] as $room): ?>
                                    <li><?= $room['name'] ?> x <?= $room['rooms_booked'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>