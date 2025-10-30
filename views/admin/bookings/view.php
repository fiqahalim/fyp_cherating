<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>
<div class="container mt-5">
    <?php Flash::display(); ?>

    <a href="<?= APP_URL . '/admin/bookings' ?>" class="btn btn-light btn-icon-split mt-3">
        <span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Back to List</span>
    </a>

    <div class="card shadow mt-3 mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Booking Details: <?= htmlspecialchars($booking['booking_ref_no']) ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($booking['full_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($booking['phone']) ?></p>
                    <p><strong>Check-In:</strong> <?= htmlspecialchars($booking['check_in']) ?></p>
                </div>

                <div class="col-md-6">
                    <p><strong>Payment Method:</strong> <?= ucfirst (htmlspecialchars($booking['payment_method'])) ?></p>
                    <p><strong>Total Amount:</strong> RM <?= number_format($booking['total_amount'], 2) ?></p>
                    <p><strong>Payment Status:</strong> <?= ucfirst(htmlspecialchars($booking['payment_status'])) ?></p>
                    <p><strong>Check-Out:</strong> <?= htmlspecialchars($booking['check_out']) ?></p>
                </div>

                <div class="col-md-12">
                    <p><strong>Notes:</strong> <?= htmlspecialchars($booking['payment_details']) ?></p>
                </div>

                <div class="col-md-12 mt-4">
                    <h4>Rooms Booked</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Room Name</th>
                                <th>Price Per Room</th>
                                <th>Rooms Booked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking['rooms'] as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['name']) ?></td>
                                    <td>RM <?= number_format($room['price'], 2) ?></td>
                                    <td><?= htmlspecialchars($room['rooms_booked']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>