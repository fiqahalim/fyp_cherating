<?php include_once __DIR__ . '/../layouts/admin_header.php'; ?>
<div class="container-fluid mt-4">
    <?php Flash::display(); ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Booking Details</h1>
        <a href="<?= APP_URL . '/admin/bookings' ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-user"></i> Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($booking['full_name']) ?></div>
                        <div class="text-muted small">Customer ID: #<?= htmlspecialchars($booking['customer_id']) ?></div>
                    </div>
                    <hr>
                    <p><strong><i class="fas fa-envelope fa-fw"></i> Email:</strong><br> <?= htmlspecialchars($booking['email']) ?></p>
                    <p><strong><i class="fas fa-phone fa-fw"></i> Phone:</strong><br> <?= htmlspecialchars($booking['phone']) ?></p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-calendar-alt"></i> Stay Details</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-right">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Check-In</div>
                            <div class="h6 mb-0 font-weight-bold"><?= date('d M Y', strtotime($booking['check_in'])) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Check-Out</div>
                            <div class="h6 mb-0 font-weight-bold"><?= date('d M Y', strtotime($booking['check_out'])) ?></div>
                        </div>
                    </div>
                    <hr>
                    <p class="text-center">
                        <span class="badge badge-dark p-2">
                            <i class="fas fa-moon"></i> Total Nights: <?= htmlspecialchars($booking['total_nights']) ?>
                        </span>
                        <span class="badge badge-dark p-2">
                            <i class="fas fa-users"></i> Guests: <?= htmlspecialchars($booking['guests']) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Reference No: <?= htmlspecialchars($booking['booking_ref_no']) ?></h6>
                    <div>
                        <?php 
                            $statusClass = [
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'cancelled' => 'danger'
                            ][$booking['status']] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?= $statusClass ?> text-uppercase p-2"><?= htmlspecialchars($booking['status']) ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <h4>Rooms Ordered</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Room Name</th>
                                    <th class="text-center">Price / Night</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $calculatedSubtotal = 0;
                                foreach ($booking['rooms'] as $room): 
                                    $roomSubtotal = $room['price'] * $room['rooms_booked'] * $booking['total_nights'];
                                    $calculatedSubtotal += $roomSubtotal;
                                ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($room['name']) ?></strong></td>
                                        <td class="text-center">RM <?= number_format($room['price'], 2) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($room['rooms_booked']) ?> Room(s)</td>
                                        <td class="text-right">RM <?= number_format($roomSubtotal, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Grand Total:</th>
                                    <th class="text-right h5 text-primary">RM <?= number_format($booking['total_amount'], 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-receipt"></i> Payment Info</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Method:</td>
                                    <td><strong><?= ucfirst(htmlspecialchars($booking['payment_method'])) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Status:</td>
                                    <td>
                                        <span class="text-<?= $booking['payment_status'] === 'paid' ? 'success' : 'danger' ?>">
                                            ● <?= ucfirst(htmlspecialchars($booking['payment_status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td><?= $booking['payment_date'] ? date('d M Y H:i', strtotime($booking['payment_date'])) : 'N/A' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 border-left">
                            <h5><i class="fas fa-sticky-note"></i> Admin Notes</h5>
                            <div class="p-2 bg-light border rounded" style="min-height: 80px;">
                                <?= nl2br(htmlspecialchars($booking['notes'] ?? 'No notes provided.')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/admin_footer.php'; ?>