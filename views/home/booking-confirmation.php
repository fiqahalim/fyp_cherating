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
            <form method="POST" action="<?= APP_URL ?>/confirm-booking" id="booking_form">
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
                                        : $uploadDir . 'default.jpg';
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
                        <p class="text-info small">It looks like you don't have an account. Please create a username and password to proceed.</p>
                        
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
                        <option value="">-- Select Payment Method --</option>
                        <option value="card" <?= get_value('payment_method') == 'card' ? 'selected' : '' ?>>Credit / Debit Card</option>
                        <option value="paypal" <?= get_value('payment_method') == 'paypal' ? 'selected' : '' ?>>PayPal</option>
                        <option value="qr" <?= get_value('payment_method') == 'qr' ? 'selected' : '' ?>>QR Pay</option>
                    </select>

                    <!-- CARD FIELDS -->
                    <div id="card_payment_details" class="payment-method-fields">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_number" class="form-label">Card Number </label>
                                <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" value="<?= get_value('card_number') ?>" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date </label>
                                <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" value="<?= get_value('card_expiry') ?>" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" maxlength="4" placeholder="123" value="<?= get_value('card_cvc') ?>" />
                            </div>
                        </div>
                    </div>

                    <!-- PayPal payment fields (will be shown if 'paypal' is selected) -->
                    <div id="paypal_payment_details" class="payment-method-fields" style="display:none;">
                        <label for="paypal_email" class="form-label">PayPal Email</label>
                        <input type="email" id="paypal_email" name="paypal_email" class="form-control" placeholder="Enter your PayPal email" value="<?= get_value('paypal_email') ?>" />
                    </div>

                    <!-- QR payment section (will be shown if 'qr' is selected) -->
                    <div id="qr_payment_details" class="payment-method-fields" style="display:none;">
                        <p>Scan the QR code below to complete your payment:</p>
                        <div id="qr_code_container"></div>
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
                <div class="d-flex justify-content-between" style="font-size:24px;">
                    <span>Price:</span>
                    <strong>RM <?= number_format($totalAmount, 2) ?></strong>
                </div>
                <p class="text-muted small mt-2 mb-0">* Additional fees may apply</p>
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