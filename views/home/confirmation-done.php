<div class="container mt-4 mb-4">
    <?php include_once __DIR__ . '/../layout/progression-bar-done.php'; ?>
    <?php Flash::display(); ?>
</div>

<div class="container mb-5 p-4 shadow rounded bg-white" style="max-width: 900px;">
    <div class="text-center">
        <img src="<?= APP_URL ?>/assets/images/checkmark.png" alt="Confirmed" width="80" class="mb-3">
        <h2 class="text-success">Booking Confirmed!</h2>
        <p class="text-primary"><strong>Booking Reference:</strong> <?= htmlspecialchars($booking['booking_ref_no']) ?></p>
        <p class="lead">Thank you, <strong><?= htmlspecialchars($booking['full_name']) ?></strong>! Your booking has been successfully confirmed.</p>
    </div>

    <?php 
        $isFPX = (strtolower($booking['payment_method']) === 'fpx');
        $isPending = (!isset($payment['verified']) || $payment['verified'] === 'pending');
        
        if ($isFPX && $isPending):
    ?>
        <div class="alert alert-warning border-warning shadow-sm mt-4 text-center">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Localhost Payment Sync</h5>
            <p>If you have already completed your payment but the status below still says <strong>PENDING</strong>, please click the button below:</p>
            <?php if (!empty($payment['billplz_id'])): ?>
                <a href="<?= APP_URL ?>/verify-payment/<?= $payment['billplz_id'] ?>" class="btn btn-warning fw-bold">
                    <i class="fas fa-sync-alt"></i> VERIFY MY PAYMENT NOW
                </a>
            <?php else: ?>
                <p class="text-danger small">Error: Bill ID not found. Please contact support.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <hr>

    <div class="row mt-4">
        <div class="col-md-6 border-end">
            <h3 class="mb-3">Your Itinerary & Details</h3>

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
        
        <div class="col-md-6">
            <div class="sidebar-card-price p-3 bg-dark rounded shadow-sm">
                <h4 class="fw-bold mb-3 text-center text-white">Price Summary</h4>

                <?php
                    $total_rooms_count = 0;
                    foreach($booking['rooms'] as $r) { $total_rooms_count += (int)$r['rooms_booked']; }
                    
                    $avg_price_per_room_night = $booking['total_amount'] / ($booking['total_nights'] * $total_rooms_count);
                ?>
                
                <?php foreach ($booking['rooms'] as $room): 
                    $room_qty = (int)$room['rooms_booked'];
                    $room_display_price = $avg_price_per_room_night * $room_qty;
                ?>
                    <div class="d-flex justify-content-between small text-muted">
                        <span class="text-light">
                            <?= $room_qty ?> × <?= htmlspecialchars($room['name']) ?> 
                            <br><small>(Avg. RM <?= number_format($avg_price_per_room_night, 2) ?> / night)</small>
                        </span>
                        <span class="text-light">RM <?= number_format($room_display_price, 2) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-between py-1 border-top mt-2 text-white">
                    <span>Subtotal (per night)</span>
                    <span class="fw-bold">RM <?= number_format($avg_price_per_room_night * $total_rooms_count, 2) ?></span>
                </div>

                <div class="d-flex justify-content-between py-3 text-danger" style="font-size:25px;">
                    <span>Grand Total:</span>
                    <strong class="text-danger">RM <?= number_format($booking['total_amount'], 2) ?></strong>
                </div>

                <div class="d-flex justify-content-between text-info fw-bold">
                    <span>Deposit Paid (35%):</span>
                    <span>RM <?= number_format($booking['deposit_paid'], 2) ?></span>
                </div>
                
                <div class="py-1">
                    <strong class="d-block text-muted">Payment Method:</strong>
                    <span class="text-white">Method: <?= strtoupper($booking['payment_method']) ?></span>
                </div>
                <div class="py-1">
                    <strong class="d-block text-muted">Verification Status:</strong>
                    <?php 
                        $status = strtoupper($payment['verified'] ?? 'PENDING');
                        $badgeClass = ($status === 'APPROVED') ? 'text-success' : 'text-warning';
                    ?>
                    <span class="<?= $badgeClass ?> fw-bold"><?= $status ?></span>
                </div>
                <div class="py-1">
                    <strong class="d-block text-muted">Booking Status:</strong>
                    <span class="text-info fw-bold"><?= ucfirst($booking['status']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <hr class="mt-5">

    <div class="text-center">
        <a href="<?= APP_URL ?>/download-invoice/<?= $booking['id'] ?>" class="btn btn-primary btn-lg me-3" target="_blank">Download Invoice</a>
        <a href="<?= APP_URL ?>" class="btn btn-outline btn-lg">Back to Home</a>
    </div>
</div>