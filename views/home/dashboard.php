<?php
/**
 * Returns a Bootstrap badge class based on booking status.
 */
function getStatusBadge($status) {
    $status = strtolower($status);
    switch ($status) {
        case 'confirmed':
            return '<span class="badge bg-success">Confirmed</span>';
        case 'pending':
            return '<span class="badge bg-warning text-dark">Pending</span>';
        case 'cancelled':
            return '<span class="badge bg-danger">Cancelled</span>';
        default:
            return '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}
?>

<?php
// ----------------------------------------------------------------------
// 1. DATA PREPARATION: Create a central JavaScript variable for all bookings
// This happens at the top to ensure the data is available before the script runs.
// ----------------------------------------------------------------------
// Combine all bookings into one array for easy JavaScript lookup
$allBookings = array_merge($upcomingBookings, $pastBookings);
?>

<script>
    // Define a JavaScript object mapping Booking ID to its full details
    const BOOKINGS_DATA = {};
    <?php foreach ($allBookings as $booking): ?>
        // Ensure data is properly JSON encoded for JavaScript usage
        BOOKINGS_DATA[<?= $booking['id'] ?>] = <?= json_encode($booking) ?>;
    <?php endforeach; ?>
</script>

<div class="container-fluid py-4">
    <h2 class="mb-4 text-primary">👋 Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Customer') ?>!</h2>
    <p class="lead text-muted">Here is a summary of your booking history with us.</p>
    
    <?php 
    // Re-check helper function if it was not in a linked file
    if (!function_exists('getStatusBadge')) {
        // ... (function definition skipped as it's already at the top)
    }
    
    // Check if the total number of bookings is zero
    $totalBookings = count($upcomingBookings) + count($pastBookings);
    ?>

    <?php if ($totalBookings === 0): ?>
        <div class="alert alert-info py-4 text-center">
            <h4 class="alert-heading">No Bookings Found</h4>
            <p>Ready for your next getaway? Start searching for rooms now!</p>
            <a href="<?= APP_URL ?>" class="btn btn-primary mt-2">Book Now</a>
        </div>
    <?php endif; ?>

    <h3 class="mt-5 mb-3 text-dark border-bottom pb-2">🗓️ Upcoming Bookings (<?= count($upcomingBookings) ?>)</h3>
    
    <?php if (empty($upcomingBookings)): ?>
        <div class="alert alert-light text-center">
            You have no confirmed upcoming bookings. Time to plan a trip!
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($upcomingBookings as $booking): 
                $total_rooms = array_sum(array_column($booking['rooms'], 'rooms_booked'));
            ?>
                <div class="col-md-12 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 booking-card-enhanced upcoming-card">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 text-white">
                                Booking Ref: <strong><?= htmlspecialchars($booking['booking_ref_no'] ?? $booking['id']) ?></strong>
                            </h5>
                            <div><?= getStatusBadge($booking['status']) ?></div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <div><small class="text-muted d-block">Check-in</small><span class="fw-bold fs-5"><?= date('D, M j', strtotime($booking['check_in'])) ?></span></div>
                                <div class="text-center"><small class="text-muted d-block">Total Nights</small><span class="fw-bold fs-5"><?= (int)$booking['total_nights'] ?></span></div>
                                <div><small class="text-muted d-block">Check-out</small><span class="fw-bold fs-5"><?= date('D, M j', strtotime($booking['check_out'])) ?></span></div>
                            </div>

                            <h6 class="text-secondary mb-2">Room Summary (<?= $total_rooms ?> Rooms)</h6>
                            <ul class="list-group list-group-flush mb-3 small">
                                <?php foreach ($booking['rooms'] as $room): ?>
                                    <li class="list-group-item d-flex justify-content-between py-1">
                                        <span><?= htmlspecialchars($room['name']) ?></span>
                                        <span>Qty: **<?= $room['rooms_booked'] ?>**</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="d-flex justify-content-between pt-2 border-top">
                                <strong class="text-dark">Total Amount:</strong>
                                <strong class="text-success fs-5">RM <?= number_format($booking['total_amount'], 2) ?></strong>
                            </div>
                        </div>

                        <div class="card-footer text-end">
                            <button type="button" 
                                    class="btn btn-primary btn-sm view-details-btn"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#bookingDetailsModal"
                                    data-booking-id="<?= $booking['id'] ?>">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h3 class="mt-5 mb-3 text-dark border-bottom pb-2">🕰️ Past Bookings (<?= count($pastBookings) ?>)</h3>

    <?php if (empty($pastBookings)): ?>
        <div class="alert alert-light text-center">
            No completed past bookings found.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($pastBookings as $booking): 
                $total_rooms = array_sum(array_column($booking['rooms'], 'rooms_booked'));
            ?>
                <div class="col-md-12 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 booking-card-enhanced past-card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0">
                                Booking Ref: <strong><?= htmlspecialchars($booking['booking_ref_no'] ?? $booking['id']) ?></strong>
                            </h5>
                            <div><?= getStatusBadge($booking['status']) ?></div>
                        </div>

                        <div class="card-body text-muted">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <div><small class="text-muted d-block">Check-in</small><span class="fw-bold fs-5"><?= date('D, M j', strtotime($booking['check_in'])) ?></span></div>
                                <div class="text-center"><small class="text-muted d-block">Total Nights</small><span class="fw-bold fs-5"><?= (int)$booking['total_nights'] ?></span></div>
                                <div><small class="text-muted d-block">Check-out</small><span class="fw-bold fs-5"><?= date('D, M j', strtotime($booking['check_out'])) ?></span></div>
                            </div>

                            <h6 class="text-secondary mb-2">Room Summary (<?= $total_rooms ?> Rooms)</h6>
                            <ul class="list-group list-group-flush mb-3 small">
                                <?php foreach ($booking['rooms'] as $room): ?>
                                    <li class="list-group-item d-flex justify-content-between py-1">
                                        <span><?= htmlspecialchars($room['name']) ?></span>
                                        <span>Qty: **<?= $room['rooms_booked'] ?>**</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="d-flex justify-content-between pt-2 border-top">
                                <strong class="text-dark">Total Amount:</strong>
                                <strong class="text-success fs-5">RM <?= number_format($booking['total_amount'], 2) ?></strong>
                            </div>
                        </div>

                        <div class="card-footer text-end">
                            <button type="button" 
                                    class="btn btn-outline-secondary btn-sm view-details-btn"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#bookingDetailsModal"
                                    data-booking-id="<?= $booking['id'] ?>">
                                View History
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="bookingDetailsModalLabel">Booking Details: <span id="modal-ref-no"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modal-content-area">Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
/* Added specific color coding for upcoming/past cards */
.upcoming-card {
    border-left: 5px solid var(--bs-dark) !important; 
}
.past-card {
    border-left: 5px solid var(--bs-primary) !important;
}

/* Ensure hover effect remains */
.booking-card-enhanced:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <script>
$(document).ready(function() {
    $('#bookingDetailsModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const bookingId = button.data('booking-id');
        const modal = $(this);
        const contentArea = modal.find('#modal-content-area');
        
        // Retrieve data from the pre-loaded global object
        const booking = BOOKINGS_DATA[bookingId];

        // Reset title and content
        modal.find('#modal-ref-no').text('');
        contentArea.html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading booking details...</p></div>');

        if (booking) {
            // Set Modal Title
            modal.find('#modal-ref-no').text(booking.booking_ref_no || booking.id);
            
            // Generate and load the detailed HTML content
            const htmlContent = buildBookingDetailHtml(booking);
            contentArea.html(htmlContent);
        } else {
            contentArea.html('<div class="alert alert-danger">Error: Booking data not found locally.</div>');
        }
    });

    // Helper function to build the detailed HTML content
    function buildBookingDetailHtml(booking) {
        var roomsHtml = booking.rooms.map(room => `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>${room.name}</strong>
                <span>${room.rooms_booked} room(s)</span>
            </li>
        `).join('');

        const ucfirst = (str) => str.charAt(0).toUpperCase() + str.slice(1);
        
        // Helper function for date formatting
        const formatDate = (dateString) => new Date(dateString).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });

        const paymentMethod = ucfirst(booking.payment_method || 'N/A');
        const paymentStatus = ucfirst(booking.payment_status || 'unpaid');
        const bookingStatus = ucfirst(booking.status || 'pending');
        
        const totalAmount = parseFloat(booking.total_amount).toFixed(2);
        
        // This structure closely mimics your confirmation-done.php page
        return `
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2 class="text-primary">Booking Details</h2>
                    <p class="lead">Ref: <strong>${booking.booking_ref_no || booking.id}</strong> | Status: ${getStatusBadgeJs(bookingStatus)}</p>
                </div>
                
                <div class="col-md-6 border-end">
                    <h5 class="text-primary mb-3">Itinerary</h5>
                    <ul class="list-unstyled">
                        <li><strong>Check-in:</strong> ${formatDate(booking.check_in)}</li>
                        <li><strong>Check-out:</strong> ${formatDate(booking.check_out)}</li>
                        <li><strong>Total Nights:</strong> ${booking.total_nights}</li>
                    </ul>
                    
                    <h5 class="text-primary mt-4 mb-3">Rooms Booked</h5>
                    <ul class="list-group mb-4">${roomsHtml}</ul>
                </div>
                
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Financial Summary</h5>
                    <div class="p-3 bg-light rounded shadow-sm">
                        <div class="d-flex justify-content-between py-2">
                            <span>Grand Total:</span>
                            <strong class="fs-4 text-success">RM ${totalAmount}</strong>
                        </div>
                        <hr class="my-1">
                        <div class="py-1">
                            <strong class="d-block text-muted">Payment Method:</strong>
                            <span>${paymentMethod} (${paymentStatus})</span>
                        </div>
                        <div class="py-1">
                            <strong class="d-block text-muted">Booking Status:</strong>
                            <span class="text-success fw-bold">${bookingStatus}</span>
                        </div>
                        <div class="py-3 text-center">
                            <a href="<?= APP_URL ?>/download-invoice/${booking.id}" class="btn btn-primary btn-sm">Download Invoice</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // JS version of getStatusBadge (must be defined in JS scope)
    function getStatusBadgeJs(status) {
        status = status.toLowerCase();
        switch (status) {
            case 'confirmed': return '<span class="badge bg-success">Confirmed</span>';
            case 'pending': return '<span class="badge bg-warning text-dark">Pending</span>';
            case 'cancelled': return '<span class="badge bg-danger">Cancelled</span>';
            default: return '<span class="badge bg-secondary">' + ucfirst(status) + '</span>';
        }
    }
});
</script>