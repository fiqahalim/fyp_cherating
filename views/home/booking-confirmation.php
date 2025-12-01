<?php
$base_url = '/fyp_cherating';
?>

<div class="container my-5">

    <?php Flash::display(); ?>

    <?php include_once __DIR__ . '/../layout/progression-bar.php'; ?>
    <div class="row">
        <!-- LEFT COLUMN – FORM -->
        <div class="col-lg-8">
            <!-- HEADER -->
            <form method="POST" action="<?= APP_URL ?>/confirm-booking">
                <input type="hidden" name="check_in" value="<?= htmlspecialchars($arrival) ?>">
                <input type="hidden" name="check_out" value="<?= htmlspecialchars($departure) ?>">
                <input type="hidden" name="total_amount" value="<?= htmlspecialchars($totalAmount) ?>">

                <!-- SELECTED ROOMS -->
                <div class="booking-box mb-4">
                    <h4 class="section-title">Your Selected Rooms</h4>

                    <?php
                        $uploadDir = $base_url . '/uploads/rooms/'; 
                    ?>

                    <?php foreach ($rooms as $room): ?>
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
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- PAYMENT -->
                <div class="booking-box mb-4">
                    <h4 class="section-title">Payment Method</h4>
                    <select id="payment_method" name="payment_method" class="form-control mb-3" required>
                        <option value="card">Credit / Debit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="qr">QR Pay</option>
                    </select>

                    <!-- CARD FIELDS -->
                    <div id="card_payment_details" class="payment-method-fields">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_number" class="form-label">Card Number </label>
                                <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date </label>
                                <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" maxlength="4" placeholder="123" />
                            </div>
                        </div>
                    </div>

                    <!-- PayPal payment fields (will be shown if 'paypal' is selected) -->
                    <div id="paypal_payment_details" class="payment-method-fields" style="display:none;">
                        <label for="paypal_email" class="form-label">PayPal Email</label>
                        <input type="email" id="paypal_email" name="paypal_email" class="form-control" placeholder="Enter your PayPal email" />
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
                    <strong>Check-in:</strong> <?= htmlspecialchars($arrival) ?>
                </div>

                <div class="mb-3">
                    <strong>Check-out:</strong> <?= htmlspecialchars($departure) ?>
                </div>
                <hr>
                <div class="fw-bold mb-2"><b>You selected:</b></div>
                <ul class="list-unstyled">
                    <?php foreach ($rooms as $room): ?>
                        <?php if ($room['quantity'] > 0 && $room['status'] === 'active'): ?>
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
    const emailInput = document.getElementById('guest_email');
    const usernameInput = document.getElementById('guest_username');
    const passwordInput = document.getElementById('guest_password');
    const newCustomerFields = document.getElementById('new_customer_fields');
    const confirmButton = document.querySelector('button[type="submit"]');

    const setValidationFeedback = (input, message, isInvalid) => {
        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.classList.add('invalid-feedback');
            input.parentNode.appendChild(feedback);
        }
        
        input.classList.toggle('is-invalid', isInvalid);
        feedback.textContent = message;
        confirmButton.disabled = isInvalid;
    };

    const checkAvailability = (field, value, checkUsername = false) => {
        if (value.length === 0) return; 

        const data = {
            email: emailInput.value.trim(),
            username: usernameInput.value.trim()
        };

        confirmButton.disabled = true;

        fetch('<?= APP_URL ?>/check-customer-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(data => {
            let conflictFound = false;
            
            if (data.emailConflict) {
                setValidationFeedback(emailInput, 'This email is already registered.', true);
                conflictFound = true;
                hideNewCustomerFields(false); 
            } else {
                setValidationFeedback(emailInput, '', false);
                showNewCustomerFields();
            }

            if (checkUsername && data.usernameConflict) {
                setValidationFeedback(usernameInput, 'This username is already taken.', true);
                conflictFound = true;
            } else if (checkUsername) {
                setValidationFeedback(usernameInput, '', false);
            }
            
            if (!conflictFound) {
                confirmButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error during availability check:', error);
            confirmButton.disabled = false;
        });
    };
    
    const showNewCustomerFields = () => {
        newCustomerFields.style.display = 'flex';
        usernameInput.setAttribute('required', 'required');
        passwordInput.setAttribute('required', 'required');
    }
    
    const hideNewCustomerFields = (clearData = true) => {
        newCustomerFields.style.display = 'none';
        usernameInput.removeAttribute('required');
        passwordInput.removeAttribute('required');
        if (clearData) {
            usernameInput.value = '';
            passwordInput.value = '';
            setValidationFeedback(usernameInput, '', false);
        }
    }
    emailInput.addEventListener('blur', function() {
        setValidationFeedback(usernameInput, '', false); 
        
        if (this.value.trim() !== '') {
            checkAvailability('email', this.value.trim(), false);
        } else {
            showNewCustomerFields();
        }
    });

    usernameInput.addEventListener('blur', function() {
        if (this.value.trim() !== '') {
            checkAvailability('username', this.value.trim(), true);
        } else {
            setValidationFeedback(usernameInput, 'Username is required.', true);
        }
    });
    
    if (emailInput.value.trim() !== '') {
        checkAvailability('email', emailInput.value.trim(), false);
    } else {
        showNewCustomerFields();
    }
});
</script>