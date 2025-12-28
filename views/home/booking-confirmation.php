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
                        // Logic for calculating quantities
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
                                $imageFilename = $room['image'] ?? ''; 
                                if (empty($imageFilename)) {
                                    $finalImagePath = APP_URL . '/uploads/rooms/default.png';
                                } else {
                                    if (strpos($imageFilename, 'uploads') === false) {
                                        $imageFilename = 'uploads/rooms/' . ltrim($imageFilename, '/');
                                    }
                                    $finalImagePath = APP_URL . '/' . ltrim($imageFilename, '/');
                                }
                            ?>
                            <div class="room-selected-card mb-3 p-3 border rounded d-flex">
                                <img src="<?= htmlspecialchars($finalImagePath) ?>" 
                                    alt="<?= htmlspecialchars($room['name']) ?>" 
                                    class="room-thumb me-3" 
                                    style="width: 100px; height: 100px; object-fit: cover;">

                                <div class="ml-3">
                                    <h5 class="mb-1"><?= htmlspecialchars($room['name']) ?></h5>
                                    <p class="text-muted small mb-1"><?= htmlspecialchars($room['description']) ?></p>

                                    <p class="mb-1">
                                        <?php
                                            $roomTotal = $room['calculated_total'] ?? ($room['price'] * $room['quantity'] * ($totalNights ?? 1));
                                            $dynamic_rate = $roomTotal / (($totalNights ?? 1) * $room['quantity']);
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
                    <?php if (isset($customerInfo) && $customerInfo): ?>
                        <div class="alert alert-info d-flex align-items-center shadow-sm">
                            <i class="fas fa-user-check fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0"><?= htmlspecialchars($customerInfo['full_name']) ?></h5>
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($customerInfo['email']) ?> | 
                                    <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($customerInfo['phone']) ?>
                                </p>
                            </div>
                        </div>

                        <input type="hidden" name="name" value="<?= htmlspecialchars($customerInfo['full_name']) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($customerInfo['email']) ?>">
                        <input type="hidden" name="phone" value="<?= htmlspecialchars($customerInfo['phone']) ?>">

                    <?php else: ?>
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
                    <?php endif; ?>
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
                            <img src="<?= $base_url ?>/assets/images/QR_Merchant.jpeg"
                                alt="Payment QR Code" 
                                style="border: 1px solid #ddd; padding: 10px; border-radius: 8px; width:350px;">
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
            <!-- BOOKING TIMER -->
             <div class="sidebar-card p-3 mb-4 border-danger border">
                <h5 class="text-danger fw-bold mb-2"><i class="fas fa-clock me-2"></i> Booking Session</h5>
                <p class="small mb-2">Please complete your payment within:</p>
                <div id="countdown-timer" class="display-6 fw-bold text-center text-danger">
                    <span class="time-text">--:--</span>
                </div>
                <p class="text-muted small mt-2 mb-0">Rooms are held temporarily and will be released if the timer expires.</p>
            </div>
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
                ?>

                <?php foreach ($rooms as $room): ?>
                    <?php if (($room['quantity'] ?? 0) > 0 && ($room['status'] ?? 'active') === 'active'): ?>
                        <?php 
                            $room_quantity = $room['quantity'];
                            $total_rooms_booked += $room_quantity;
                            $room_subtotal = $room['calculated_total'] ?? ($room['price'] * $room_quantity * $totalNights);
                            $avg_night_rate = $room_subtotal / ($totalNights * $room_quantity);
                        ?>
                        <div class="d-flex justify-content-between small mb-2">
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

                <div class="d-flex justify-content-between small">
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
    const paymentMethodSelect = document.getElementById('payment_method');
    const qrDetails = document.getElementById('qr_payment_details');
    const fpxDetails = document.getElementById('fpx_payment_details');
    const receiptInput = document.getElementById('receipt');

    let timeLeft = <?= $_SESSION['booking_expires_at'] - time() ?>;
    const timerDisplay = document.getElementById('countdown-timer');
    
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
        updatePaymentFields();
    }

    // Booking timer
    function updateTimer() {
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            timerDisplay.innerHTML = "EXPIRED";
            
            // Alert the user and redirect
            alert("Your booking session has expired. You will be redirected to the room selection page.");
            window.location.href = "<?= APP_URL ?>/rooms";
            return;
        }

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;

        // Format as MM:SS (e.g., 09:05)
        timerDisplay.innerHTML = 
            (minutes < 3 ? '0' : '') + minutes + ":" + 
            (seconds < 3 ? '0' : '') + seconds;

        // Add a pulsing effect when less than 1 minute remains
        if (timeLeft < 60) {
            timerDisplay.classList.add('timer-pulse');
        }
        timeLeft--;
    }
    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
});
</script>