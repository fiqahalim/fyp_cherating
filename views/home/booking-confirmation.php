<?php
$base_url = '/fyp_cherating';

$show_fields_flag = isset($_SESSION['show_new_customer_fields']);
$pending_data = $_SESSION['pending_booking_data'] ?? [];

// Clear the session flag and pending data after reading them
if (isset($_SESSION['show_new_customer_fields'])) {
    unset($_SESSION['show_new_customer_fields']);
}

// Helper to safely get value from pending data
function get_value($key, $default = '') {
    global $pending_data;
    return htmlspecialchars($pending_data[$key] ?? $default);
}

// Get the existing total amount if available
$total_amount = $_SESSION['total_amount'] ?? 0.0;
$totalNights = $totalNights ?? $_SESSION['total_nights'] ?? 0;
?>

<!-- ============================================================ -->
<!-- Hidden input field for JavaScript to check the flag set by the controller -->
<!-- ============================================================ -->
<input type="hidden" id="show_new_customer_fields_flag" value="<?= $show_fields_flag ? '1' : '0' ?>">

<div class="container my-5">

    <?php Flash::display(); ?>

    <?php include_once __DIR__ . '/../layout/progression-bar.php'; ?>
    <div class="row">
        <!-- LEFT COLUMN – FORM -->
        <div class="col-lg-8">
            <!-- HEADER -->
            <form method="POST" action="<?= APP_URL ?>/confirm-booking" id="booking_form" enctype="multipart/form-data">
                <!-- HIDDEN INPUTS for Booking Data -->
                <input type="hidden" name="check_in" value="<?= get_value('check_in', $_SESSION['check_in'] ?? '') ?>">
                <input type="hidden" name="check_out" value="<?= get_value('check_out', $_SESSION['check_out'] ?? '') ?>">
                <input type="hidden" name="total_amount" value="<?= $total_amount ?>">

                <!-- SELECTED ROOMS -->
                <div class="booking-box mb-4">
                    <h4 class="section-title">Your Selected Rooms</h4>

                    <?php
                        $uploadDir = $base_url . '/uploads/rooms/';
                        $current_rooms = isset($pending_data['rooms']) ? $pending_data['rooms'] : ($rooms ?? []);
                    ?>

                    <?php 
                    // This block generates the display and the hidden inputs for the rooms
                    $rooms_to_display = $rooms ?? [];

                    if (!empty($pending_data['rooms'])) {
                        foreach ($rooms_to_display as &$room) {
                            $roomId = (int)$room['id'];
                            $room['quantity'] = (int)($pending_data['rooms'][$roomId] ?? 0);
                        }
                        unset($room);
                    }
                    ?>

                    <?php foreach ($rooms_to_display as $room): ?>
                        <?php if ($room['quantity'] > 0 && $room['status'] === 'active'): ?>
                            <?php 
                            $imageFilename = $room['image']; 
                            $imagePath = !empty($imageFilename) 
                                        ? $uploadDir . $imageFilename 
                                        : $uploadDir . 'default.png';
                            ?>

                            <div class="room-selected-card mb-3 p-3 border rounded d-flex">
                                <img src="<?= htmlspecialchars($imagePath) ?>" 
                                    alt="<?= htmlspecialchars($room['name']) ?>" 
                                    class="room-thumb me-3">

                                <div class="ml-3">
                                    <h5 class="mb-1"><?= htmlspecialchars($room['name']) ?></h5>
                                    <p class="text-muted small mb-1"><?= htmlspecialchars($room['description']) ?></p>

                                    <p class="mb-1">
                                        <?php
                                            $roomTotal = $room['calculated_total'] ?? ($room['price'] * $room['quantity'] * $totalNights);
                                            $dynamic_rate = $roomTotal / ($totalNights * $room['quantity']);
                                        ?>
                                        <strong>RM <?= number_format($dynamic_rate, 2) ?></strong> per night 
                                        × <?= (int)$room['quantity'] ?> room(s)
                                    </p>

                                    <input type="hidden" 
                                        name="rooms[<?= (int)$room['id'] ?>]" 
                                        value="<?= (int)$room['quantity'] ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- GUEST DETAILS -->
                <div class="booking-box mb-4">
                    <h4 class="section-title">Your Details</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="<?= get_value('name') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required value="<?= get_value('phone') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="guest_email" class="form-control" required 
                                value="<?= get_value('email') ?>">
                        </div>
                    </div>

                    <!-- NEW CUSTOMER FIELDS (Hidden by default, shown if flag is set) -->
                    <div id="new_customer_fields" class="row mt-3" style="display:none;">
                        <p class="text-danger small">It looks like you don't have an account. Please create a username and password to proceed.</p>
                        
                        <div class="col-md-6 mb-3">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="guest_username" class="form-control" value="<?= get_value('username') ?>" />
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="guest_password" class="form-control" value="" />
                        </div>
                    </div>
                </div>

                <!-- PAYMENT -->
                <div class="booking-box mb-4">
                    <h4 class="section-title">Payment Method</h4>
                    <select id="payment_method" name="payment_method" class="form-control mb-3" required>
                        <option value="qr">QR Pay (Manual Verification)</option>
                        <option value="fpx" selected>Online Banking / FPX (Billplz)</option>
                    </select>

                    <div id="qr_payment_details" class="payment-method-fields" style="display:none;">
                        <p>Scan the QR code below to complete your deposit payment:</p>
                        <div id="qr_code_container" class="mt-3">
                            <?php
                                $displayQrLink = $qrUrl ?? $_SESSION['qr_url_raw'] ?? ''; 
                            ?>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($displayQrLink) ?>" 
                                alt="Payment QR Code" 
                                style="border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
                        </div>
                        <div class="mt-3" id="receipt_upload_wrapper">
                            <label for="receipt" class="form-label"><strong>Upload Payment Receipt (Required)</strong></label>
                            <input type="file" name="receipt" id="receipt" class="form-control" accept="image/*,.pdf">
                            <small class="text-danger">Please upload a screenshot or PDF of your transaction.</small>
                        </div>
                    </div>

                    <div id="fpx_payment_details" class="payment-method-fields" style="display:none;">
                        <div class="alert alert-info">
                            You will be redirected to the <strong>Billplz Secure Payment Gateway</strong> to complete your transaction via FPX.
                        </div>
                    </div>
                </div>

                <!-- CONFIRM BUTTON -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Confirm Booking
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT SIDEBAR -->
        <div class="col-lg-4">
            <!-- BOOKING SUMMARY CARD -->
            <div class="sidebar-card p-3 mb-4">
                <h4 class="mb-3 fw-bold">Booking Summary</h4>
                <div class="mb-2">
                    <strong>Check-in:</strong> <?= htmlspecialchars($arrival ?? $_SESSION['check_in']) ?>
                </div>
                <div class="mb-3">
                    <strong>Check-out:</strong> <?= htmlspecialchars($departure ?? $_SESSION['check_out']) ?>
                </div>
                <div class="mb-3">
                    <strong>Total Guests:</strong> <?= (int)($guests ?? $_SESSION['guests'] ?? 0) ?> Person(s)
                </div>
                <div class="mb-3">
                    <strong>Total Night(s):</strong> 
                    <span class="fw-bold"><?= (int)$totalNights ?></span>
                </div>
                <hr>
                <div class="fw-bold mb-2"><b>You selected:</b></div>
                <ul class="list-unstyled">
                    <?php foreach ($rooms as $room): ?>
                        <?php if (($room['quantity'] ?? 0) > 0 && ($room['status'] ?? 'active') === 'active'): ?>
                            <li class="mb-1">
                                <?= $room['quantity'] ?> × <?= htmlspecialchars($room['name']) ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- PRICE SUMMARY CARD -->
            <div class="sidebar-card-price p-3">
                <h4 class="fw-bold mb-3">Your Price Summary</h4>

                <?php 
                $total_rooms_booked = 0; 
                // We use the $totalAmount calculated by the controller logic
                ?>

                <?php foreach ($rooms as $room): ?>
                    <?php if (($room['quantity'] ?? 0) > 0 && ($room['status'] ?? 'active') === 'active'): ?>
                        <?php 
                            $room_quantity = $room['quantity'];
                            $total_rooms_booked += $room_quantity;
                            $room_subtotal = $room['calculated_total'] ?? ($room['price'] * $room_quantity * $totalNights);
                            $avg_night_rate = $room_subtotal / ($totalNights * $room_quantity);
                        ?>
                        <div class="d-flex justify-content-between small text-muted mb-2">
                            <span>
                                <?= $room_quantity ?> × <?= htmlspecialchars($room['name']) ?> 
                                <br><small>(Avg. RM <?= number_format($avg_night_rate, 2) ?> / night)</small>
                            </span>
                            <span>RM <?= number_format($room_subtotal, 2) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-between py-1 border-top">
                    <span>Grand Total (<?= (int)$totalNights ?> nights)</span>
                    <span class="fw-bold">RM <?= number_format($totalAmount, 2) ?></span>
                </div>

                <hr class="mt-2 mb-2">

                <div class="d-flex justify-content-between text-primary">
                    <span><strong>Deposit to Pay Now (35%):</strong></span>
                    <strong>RM <?= number_format($totalAmount * 0.35, 2) ?></strong>
                </div>

                <div class="d-flex justify-content-between text-muted small">
                    <span>Balance at Check-in:</span>
                    <span>RM <?= number_format($totalAmount - ($totalAmount * 0.35), 2) ?></span>
                </div>

                <hr class="mt-2 mb-2">

                <div class="d-flex justify-content-between text-danger" style="font-size:22px;">
                    <span>Total Amount:</span>
                    <strong class="text-danger">RM <?= number_format($totalAmount, 2) ?></strong>
                </div>

                <p class="text-muted small mt-2 mb-0">
                    * Total covers **<?= (int)$totalNights ?> night(s)** for **<?= $total_rooms_booked ?> room(s)**.
                </p>
                <p class="text-muted small mb-0">* Surcharges for weekends/holidays are included in the average rate.</p>
            </div>

            <!-- Review Rules -->
            <div class="sidebar-card p-3 mb-4 mt-4">
                <h4 class="mb-3 fw-bold">Review house rules</h4>
                <div class="mb-2">
                    Your host would like you to agree to the following house rules:
                </div>
                <ul class="list-rules">
                    <li>No smoking</li>
                    <li>No parties/events</li>
                    <li>Quiet hours are between 23:00 and 07:00</li>
                    <li>Pets are not allowed</li>
                </ul>
                <div class="mb-2">
                    By continuing to the next step, you are agreeing to these house rules.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Define all elements first (Add checks to prevent null errors)
    const paymentMethodSelect = document.getElementById('payment_method'); // Make sure your <select> has id="payment_method"
    const qrDetails = document.getElementById('qr_payment_details');
    const fpxDetails = document.getElementById('fpx_payment_details');
    const receiptInput = document.getElementById('receipt');
    
    const newCustomerFields = document.getElementById('new_customer_fields');
    const usernameInput = document.getElementById('guest_username');
    const passwordInput = document.getElementById('guest_password');
    const showFieldsElement = document.getElementById('show_new_customer_fields_flag');

    // 2. Two-Step Customer Fields Logic
    const showFields = showFieldsElement ? (showFieldsElement.value === '1') : false;

    if (showFields && newCustomerFields) {
        newCustomerFields.style.display = 'flex'; 
        if (usernameInput) usernameInput.setAttribute('required', 'required');
        if (passwordInput) passwordInput.setAttribute('required', 'required');
        newCustomerFields.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // 3. Payment Details Visibility Handler
    function updatePaymentFields() {
        if (!paymentMethodSelect) return;

        const selectedMethod = paymentMethodSelect.value;
        const qrDetails = document.getElementById('qr_payment_details');
        const fpxDetails = document.getElementById('fpx_payment_details');
        const receiptInput = document.getElementById('receipt');
        
        // Use Optional Chaining or check existence to prevent "null" errors
        if (selectedMethod === 'qr') {
            qrDetails.style.display = 'block';
            fpxDetails.style.display = 'none';
            receiptInput.setAttribute('required', 'required');
        } else {
            qrDetails.style.display = 'none';
            fpxDetails.style.display = 'block';
            receiptInput.removeAttribute('required');
            receiptInput.value = "";
        }
    }

    // 4. Attach Event Listener safely
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', updatePaymentFields);
        // Run once on page load to set initial state
        updatePaymentFields();
    }
});
</script>