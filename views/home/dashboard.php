<?php
/**
 * Helpers
 */
function getStatusBadge($status) {
    $status = strtolower($status);
    switch ($status) {
        case 'confirmed': return '<span class="badge bg-success">Confirmed</span>';
        case 'pending':   return '<span class="badge bg-warning text-dark">Pending</span>';
        case 'cancelled': return '<span class="badge bg-danger">Cancelled</span>';
        default:          return '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}

function canCancelWithRefund($checkInDate) {
    $today = new DateTime();
    $checkIn = new DateTime($checkInDate);
    $interval = $today->diff($checkIn);
    $daysRemaining = (int)$interval->format("%r%a");
    return ($daysRemaining >= 5);
}

$allBookings = array_merge($upcomingBookings ?? [], $pastBookings ?? []);
?>

<script>
    const BOOKINGS_DATA = {};
    <?php foreach ($allBookings as $booking):
        $displayDeposit = $booking['deposit_paid'] ?? 0;
        $displayMethod = !empty($booking['payment_method']) ? $booking['payment_method'] : 'Manual/QR';
    ?>
        BOOKINGS_DATA[<?= $booking['id'] ?>] = <?= json_encode(array_merge($booking, [
            'deposit_paid' => $displayDeposit,
            'payment_method' => $displayMethod
        ])) ?>;
    <?php endforeach; ?>
</script>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-0">👋 Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Customer') ?>!</h2>
            <p class="lead text-muted">Manage your guesthouse stays and payments.</p>
        </div>
        <?php if (!empty($allBookings)): ?>
            <a href="<?= APP_URL ?>/rooms" class="btn btn-success shadow-sm px-4" style="border-radius: 20px;">
                <i class="fas fa-plus"></i> New Booking
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($allBookings)): ?>
        <div class="alert alert-info py-4 text-center border-0 shadow-sm">
            <h4 class="alert-heading">No Bookings Found</h4>
            <p>Ready for your next getaway? Start searching for rooms now!</p>
            <a href="<?= APP_URL ?>/rooms" class="btn btn-primary shadow-sm px-4 mt-2">Book Now</a>
        </div>
    <?php else: ?>

    <div class="accordion border-0 shadow-sm" id="bookingAccordion">
        
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header" id="headingUpcoming">
                <button class="accordion-button bg-dark text-white rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUpcoming" aria-expanded="true">
                    <span class="me-2">🗓️</span> Upcoming Bookings (<?= count($upcomingBookings) ?>)
                </button>
            </h2>
            <div id="collapseUpcoming" class="accordion-collapse collapse show" data-bs-parent="#bookingAccordion">
                <div class="accordion-body bg-light">
                    <?php if (empty($upcomingBookings)): ?>
                        <div class="alert alert-light text-center border-0">You have no upcoming stays.</div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($upcomingBookings as $booking): 
                                $total_rooms = array_sum(array_column($booking['rooms'], 'rooms_booked'));
                                $isPaid = (strtolower($booking['payment_status']) === 'paid');
                                $allowCancel = canCancelWithRefund($booking['check_in']);
                                $deposit = $booking['deposit_paid'] ?? 0;
                            ?>
                                <div class="col-md-12 col-lg-6 mb-4">
                                    <div class="card h-100 shadow-sm border-0 booking-card-enhanced upcoming-card">
                                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                                            <h5 class="mb-0 text-white">Ref: <strong><?= htmlspecialchars($booking['booking_ref_no']) ?></strong></h5>
                                            <div><?= getStatusBadge($booking['status']) ?></div>
                                        </div>

                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2 text-center">
                                                <div><small class="text-muted d-block">Check-in</small><span class="fw-bold fs-6"><?= date('D, M j', strtotime($booking['check_in'])) ?></span></div>
                                                <div><small class="text-muted d-block">Nights</small><span class="fw-bold fs-6"><?= $booking['total_nights'] ?></span></div>
                                                <div><small class="text-muted d-block">Check-out</small><span class="fw-bold fs-6"><?= date('D, M j', strtotime($booking['check_out'])) ?></span></div>
                                            </div>

                                            <div class="p-3 mb-3 rounded <?= $isPaid ? 'bg-light border' : 'bg-warning-subtle border border-warning' ?>">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small fw-bold">Grand Total:</span>
                                                    <strong class="text-dark">RM <?= number_format($booking['total_amount'], 2) ?></strong>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="small fw-bold text-info">Deposit Paid (via <?= strtoupper($booking['payment_method'] ?: 'QR') ?>):</span>
                                                    <span class="text-info fw-bold">RM <?= number_format($booking['deposit_paid'], 2) ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center border-top pt-1">
                                                    <span class="small">Payment Status:</span>
                                                    <span class="badge <?= $isPaid ? 'bg-success' : 'bg-danger' ?> text-white"><?= strtoupper($booking['payment_status']) ?></span>
                                                </div>

                                                <?php if (!$isPaid && $booking['status'] !== 'cancelled'): ?>
                                                    <form action="<?= APP_URL ?>/booking/upload-receipt" method="POST" enctype="multipart/form-data" class="mt-3 p-2 bg-white rounded border">
                                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                        <label class="small fw-bold mb-1"><i class="fas fa-upload"></i> Upload Full Payment Receipt:</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="file" name="receipt_image" class="form-control" required accept="image/*,.pdf">
                                                            <button class="btn btn-primary" type="submit">Upload</button>
                                                        </div>
                                                    </form>
                                                <?php endif; ?>
                                            </div>

                                            <h6 class="text-secondary mb-2 small">Room Summary (<?= $total_rooms ?> Rooms)</h6>
                                            <ul class="list-group list-group-flush mb-3 small">
                                                <?php foreach ($booking['rooms'] as $room): ?>
                                                    <li class="list-group-item d-flex justify-content-between py-1 bg-transparent">
                                                        <span><?= htmlspecialchars($room['name']) ?></span>
                                                        <span>Qty: <strong><?= $room['rooms_booked'] ?></strong></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>

                                        <div class="card-footer d-flex justify-content-between bg-white border-top-0 pb-3">
                                            <button type="button" class="btn btn-primary btn-sm view-details-btn px-3" data-bs-toggle="modal" data-bs-target="#bookingDetailsModal" data-booking-id="<?= $booking['id'] ?>">
                                                View Details
                                            </button>

                                            <?php if ($booking['status'] !== 'cancelled'): ?>
                                                <?php if ($allowCancel): ?>
                                                    <a href="<?= APP_URL ?>/booking/cancel/<?= $booking['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Confirm cancellation? Since check-in is more than 5 days away, your deposit will be refunded.')">
                                                        Cancel & Refund
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small align-self-center"><i class="fas fa-lock"></i> Non-refundable</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="accordion-item border-0">
            <h2 class="accordion-header" id="headingPast">
                <button class="accordion-button collapsed bg-secondary text-white rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePast" aria-expanded="false">
                    <span class="me-2">🕰️</span> Past Bookings (<?= count($pastBookings) ?>)
                </button>
            </h2>
            <div id="collapsePast" class="accordion-collapse collapse" data-bs-parent="#bookingAccordion">
                <div class="accordion-body">
                    <?php if (empty($pastBookings)): ?>
                        <p class="text-center text-muted">No past booking history found.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($pastBookings as $booking): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card shadow-sm border-0 past-card">
                                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block">Ref: <?= $booking['booking_ref_no'] ?></small>
                                                <span class="fw-bold"><?= date('M Y', strtotime($booking['check_in'])) ?> stay</span>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bookingDetailsModal" data-booking-id="<?= $booking['id'] ?>">
                                                View
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white">Booking: <span id="modal-ref-no"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-content-area"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reviews -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="reviewModalLabel">Rate Stay: <span id="review-room-name"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= APP_URL ?>/submit-review" method="POST">
        <div class="modal-body">
            <input type="hidden" name="room_id" id="modal_review_room_id">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Rating</label>
                <select name="rating" class="form-select" required>
                    <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                    <option value="4">⭐⭐⭐⭐ Very Good</option>
                    <option value="3">⭐⭐⭐ Average</option>
                    <option value="2">⭐⭐ Poor</option>
                    <option value="1">⭐ Terrible</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Your Review</label>
                <textarea name="comment" class="form-control" placeholder="How was your stay?" rows="4" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning fw-bold">Submit Review</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    const detailModal = document.getElementById('bookingDetailsModal');
    const reviewModal = document.getElementById('reviewModal');
    
    // review modal
    if (reviewModal) {
        reviewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const roomId = button.getAttribute('data-room-id');
            const roomName = button.getAttribute('data-room-name');
            
            document.getElementById('modal_review_room_id').value = roomId;
            document.getElementById('review-room-name').textContent = roomName;
        });
    }

    // detail modal
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const bookingId = button.getAttribute('data-booking-id');
            const booking = BOOKINGS_DATA[bookingId];
            
            if (booking) {
                $('#modal-ref-no').text(booking.booking_ref_no);
                $('#modal-content-area').html(buildBookingDetailHtml(booking));
            }
        });
    }

    function buildBookingDetailHtml(booking) {
        let totalRoomsCount = 0;
        booking.rooms.forEach(r => totalRoomsCount += parseInt(r.rooms_booked));
        
        const avgPrice = booking.total_amount / (booking.total_nights * totalRoomsCount);
        
        const depositPaid = parseFloat(booking.deposit_paid || 0);
        const payMethod = (booking.payment_method || 'QR').toUpperCase();

        let roomsPriceListHtml = booking.rooms.map(room => {
            const roomQty = parseInt(room.rooms_booked);
            const roomDisplayPrice = avgPrice * roomQty;
            return `
                <div class="d-flex justify-content-between small mb-2 border-bottom pb-1">
                    <span>
                        ${roomQty} × ${room.name} 
                        <br><small class="text-muted">(Avg. RM ${avgPrice.toFixed(2)} / night)</small>
                    </span>
                    <span class="fw-bold">RM ${roomDisplayPrice.toFixed(2)}</span>
                </div>
            `;
        }).join('');

        const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });

        return `
            <div class="row">
                <div class="col-md-6 border-end">
                    <h5 class="text-primary border-bottom pb-2">Stay Information</h5>
                    <p class="mb-1"><strong>Check-in:</strong> ${formatDate(booking.check_in)}</p>
                    <p class="mb-1"><strong>Check-out:</strong> ${formatDate(booking.check_out)}</p>
                    <p class="mb-3"><strong>Total Nights:</strong> ${booking.total_nights}</p>
                    
                    <h5 class="text-primary border-bottom pb-2">Price Breakdown</h5>
                    ${roomsPriceListHtml}
                    <div class="d-flex justify-content-between mt-2">
                        <span>Subtotal (per night)</span>
                        <span class="fw-bold">RM ${(avgPrice * totalRoomsCount).toFixed(2)}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-dark rounded shadow-sm h-100">
                        <h5 class="text-white text-center mb-4 border-bottom pb-2">Price Summary</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5 text-white">Grand Total:</span>
                            <span class="fs-4 fw-bold text-white">RM ${parseFloat(booking.total_amount).toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between text-info fw-bold mb-3">
                            <span>Deposit Paid (${payMethod}):</span>
                            <span>RM ${depositPaid.toFixed(2)}</span>
                        </div>
                        <hr class="bg-secondary">
                        <div class="small mb-2 text-white">
                            <span class="d-block text-muted">Payment Details:</span>
                            <strong>Method:</strong> ${payMethod} | 
                            <strong>Status:</strong> <span class="text-info">${booking.payment_status.toUpperCase()}</span>
                        </div>
                        <div class="mt-4">
                            <a href="<?= APP_URL ?>/download-invoice/${booking.id}" class="btn btn-outline-light btn-sm w-100" target="_blank">Download Invoice</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
});
</script>