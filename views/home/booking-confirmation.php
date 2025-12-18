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
                                        <strong>RM <?= number_format($room['price'], 2) ?></strong> per night 
                                        × <?= $room['quantity'] ?> room(s)
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
                        <option value="qr" selected>QR Pay</option>
                        
                        <option value="card" disabled style="display:none;">Credit / Debit Card</option>
                        <option value="paypal" disabled style="display:none;">PayPal</option>
                    </select>
                    
                    <div id="card_payment_details" class="payment-method-fields" style="display:none;">
                        </div>

                    <div id="paypal_payment_details" class="payment-method-fields" style="display:none;">
                        </div>

                    <div id="qr_payment_details" class="payment-method-fields" style="display:block;">
                        <p>Scan the QR code below to complete your deposit payment:</p>
                        <div id="qr_code_container" class="mt-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($qrUrl ?? ''); ?>" 
                                alt="Payment QR Code" 
                                style="border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
                        </div>
                        <div class="mt-3">
                            <label for="receipt" class="form-label"><strong>Upload Payment Receipt (Required)</strong></label>
                            <input type="file" name="receipt" id="receipt" class="form-control" accept="image/*,.pdf" required>
                            <small class="text-muted">Please upload a screenshot or PDF of your transaction.</small>
                        </div>
                        <p class="mt-2"><small>Please ensure the payment is successful before clicking "Confirm Booking".</small></p>
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

                <?php $total_room_cost = 0; ?>
                <?php $total_rooms_booked = 0; ?>

                <?php foreach ($rooms as $room): ?>
                    <?php if (($room['quantity'] ?? 0) > 0 && ($room['status'] ?? 'active') === 'active'): ?>
                        <?php 
                            $room_price_per_night = $room['price'];
                            $room_quantity = $room['quantity'];
                            $room_subtotal = $room_price_per_night * $room_quantity;
                            $total_room_cost += $room_subtotal;
                            $total_rooms_booked += $room_quantity;
                        ?>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>
                                <?= (int)$room_quantity ?> × <?= htmlspecialchars($room['name']) ?> 
                                (@ RM<?= number_format($room_price_per_night, 2) ?> / night)
                            </span>
                            <span>RM <?= number_format($room_subtotal, 2) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span>Total Room Cost</span>
                    <span class="fw-bold">RM <?= number_format($total_room_cost, 2) ?></span>
                </div>

                <div class="d-flex justify-content-between py-1">
                    <span>Total Nights</span>
                    <span class="fw-bold">x <?= (int)$totalNights ?></span>
                </div>

                <hr class="mt-1 mb-2">

                <div class="d-flex justify-content-between text-danger" style="font-size:25px;">
                    <span>Grand Total:</span>
                    <strong class="text-danger">RM <?= number_format($total_amount, 2) ?></strong>
                </div>

                <p class="text-muted small mt-2 mb-0">
                    * Total amount covers **<?= (int)$totalNights ?> night(s)** for **<?= $total_rooms_booked ?> room(s)**.
                </p>
                <p class="text-muted small mb-0">* Additional fees (e.g., local taxes) may apply upon check-in.</p>
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
    // 1. Two-Step Customer Fields Logic
    const newCustomerFields = document.getElementById('new_customer_fields');
    const usernameInput = document.getElementById('guest_username');
    const passwordInput = document.getElementById('guest_password');
    const showFieldsElement = document.getElementById('show_new_customer_fields_flag');
    
    // Check the flag set by the PHP logic (1 == true, 0 == false)
    const showFields = showFieldsElement ? (showFieldsElement.value === '1') : false;

    if (showFields) {
        // Step 2: Controller prompted the user for credentials. Show the fields.
        newCustomerFields.style.display = 'flex'; 
        
        // Make them required (Crucial for the second submission)
        if (usernameInput) usernameInput.setAttribute('required', 'required');
        if (passwordInput) passwordInput.setAttribute('required', 'required');
        
        // Scroll to the fields for immediate attention
        newCustomerFields.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // 2. Payment Details Visibility Handler
    const paymentMethodSelect = document.getElementById('payment_method');
    const cardDetails = document.getElementById('card_payment_details');
    const paypalDetails = document.getElementById('paypal_payment_details');
    const qrDetails = document.getElementById('qr_payment_details');

    const cardFields = [
        document.getElementById('card_number'), 
        document.getElementById('card_expiry'), 
        document.getElementById('card_cvc')
    ];
    const paypalFields = [document.getElementById('paypal_email')];

    function updatePaymentFields() {
        const selectedMethod = paymentMethodSelect.value;
        
        // Hide all fields initially
        cardDetails.style.display = 'none';
        paypalDetails.style.display = 'none';
        qrDetails.style.display = 'none';

        // Remove 'required' from all dynamic fields first
        [...cardFields, ...paypalFields].forEach(el => {
            if (el) el.removeAttribute('required');
        });

        // Show fields based on selection and set 'required'
        if (selectedMethod === 'card') {
            cardDetails.style.display = 'block';
            cardFields.forEach(el => { if (el) el.setAttribute('required', 'required'); });
        } else if (selectedMethod === 'paypal') {
            paypalDetails.style.display = 'block';
            paypalFields.forEach(el => { if (el) el.setAttribute('required', 'required'); });
        } else if (selectedMethod === 'qr') {
            qrDetails.style.display = 'block';
            // QR payment requires no extra fields
        }
    }

    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', updatePaymentFields);
        // Initialize fields on load (to show the correct fields if the form was reloaded)
        updatePaymentFields();
    }
});
</script>