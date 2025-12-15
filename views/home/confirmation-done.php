<div class="container mt-4 mb-4">
<?php include_once __DIR__ . '/../layout/progression-bar-done.php'; ?>
</div>

<div class="container mb-5 p-4 shadow rounded bg-white" style="max-width: 900px;">
    <div class="text-center">
        <img src="<?= APP_URL ?>/assets/images/checkmark.png" alt="Confirmed" width="80" class="mb-3">
        <h2 class="text-success">Booking Confirmed!</h2>
        <p><strong>Booking Reference:</strong> <?= htmlspecialchars($booking['booking_ref_no']) ?></p>
        <p class="lead">Thank you, <strong><?= htmlspecialchars($booking['full_name']) ?></strong>! Your booking has been successfully confirmed.</p>
    </div>

    <hr>

    <div class="row mt-4">
        <div class="col-md-7 border-end">
            <h3 class="text-primary mb-3">Your Itinerary & Details</h3>

            <div class="d-flex justify-content-between p-3 mb-3 bg-light rounded">
                <div>
                    <strong class="d-block text-muted">Check-in:</strong>
                    <span class="fs-5"><?= date('D, M j, Y', strtotime($booking['check_in'])) ?></span>
                </div>
                <div>
                    <strong class="d-block text-muted">Check-out:</strong>
                    <span class="fs-5"><?= date('D, M j, Y', strtotime($booking['check_out'])) ?></span>
                </div>
                <div>
                    <strong class="d-block text-muted">Total Stay:</strong>
                    <span class="fs-5 text-dark"><?= (int)$booking['total_nights'] ?> Night(s)</span>
                </div>
            </div>

            <h5 class="mt-4 mb-3 text-secondary">Rooms Booked (<?= count($booking['rooms']) ?> Types)</h5>
            <ul class="list-group mb-4">
                <?php 
                $total_rooms_booked = 0;
                foreach ($booking['rooms'] as $room): 
                    $total_rooms_booked += (int)$room['rooms_booked'];
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong class="text-dark"><?= htmlspecialchars($room['name']) ?></strong>
                        <span><?= $room['rooms_booked'] ?> room(s)</span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h5 class="mt-4 mb-3 text-secondary">Contact Information</h5>
            <ul class="list-unstyled">
                <li><strong>Name:</strong> <?= htmlspecialchars($booking['full_name']) ?></li>
                <li><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></li>
                <li><strong>Phone:</strong> <?= htmlspecialchars($booking['phone']) ?></li>
            </ul>
        </div>
        
        <div class="col-md-5">
            <div class="sidebar-card-price p-3 bg-light rounded shadow-sm">
                <h4 class="fw-bold mb-3 text-center text-primary">Price Summary</h4>
                
                <?php 
                $cost_per_night = 0;
                foreach ($booking['rooms'] as $room): 
                    $room_subtotal = (float)$room['price'] * (int)$room['rooms_booked'];
                    $cost_per_night += $room_subtotal;
                ?>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>
                            <?= (int)$room['rooms_booked'] ?> × <?= htmlspecialchars($room['name']) ?> 
                            (@ RM<?= number_format($room['price'], 2) ?> / night)
                        </span>
                        <span>RM <?= number_format($room_subtotal, 2) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-between py-1 border-top mt-2">
                    <span>Total Room Cost (1 Night)</span>
                    <span class="fw-bold">RM <?= number_format($cost_per_night, 2) ?></span>
                </div>
                
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span>Total Nights</span>
                    <span class="fw-bold">x <?= (int)$booking['total_nights'] ?></span>
                </div>

                <div class="d-flex justify-content-between py-3 text-danger" style="font-size:25px;">
                    <span>Grand Total:</span>
                    <strong class="text-dark">RM <?= number_format($booking['total_amount'], 2) ?></strong>
                </div>
                
                <div class="py-1">
                    <strong class="d-block text-muted">Payment Method:</strong>
                    <span><?= ucfirst($booking['payment_method']) ?> (<?= ucfirst($booking['payment_status']) ?>)</span>
                </div>
                <div class="py-1">
                    <strong class="d-block text-muted">Booking Status:</strong>
                    <span class="text-success fw-bold"><?= ucfirst($booking['status']) ?></span>
                </div>

            </div>
        </div>
    </div>

    <hr class="mt-5">

    <div class="text-center">
        <a href="<?= APP_URL ?>/download-invoice/<?= $booking['id'] ?>" class="btn btn-primary btn-lg me-3">Download Invoice</a>
        <a href="<?= APP_URL ?>" class="btn btn-outline btn-lg">Back to Home</a>
    </div>
</div>