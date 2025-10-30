<div class="container mt-5">
    <?php Flash::display(); ?>
    <h2>Booking Confirmation</h2>

    <form method="POST" action="<?= APP_URL ?>/confirm-booking">
        <!-- Hidden Fields for Arrival & Departure Dates -->
        <input type="hidden" name="check_in" value="<?= htmlspecialchars($arrival) ?>">
        <input type="hidden" name="check_out" value="<?= htmlspecialchars($departure) ?>">

        <!-- Room Details Section -->
        <div class="mb-4">
            <h4>Selected Rooms:</h4>
            <div class="row">
                <?php foreach ($rooms as $room): ?>
                    <?php if ($room['quantity'] > 0 && $room['status'] === 'active'): ?> <!-- Only show active rooms -->
                        <div class="col-12 col-md-6 mb-3">
                            <div class="card">
                                <img src="<?= htmlspecialchars($room['image'] ?? 'path/to/default-image.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($room['name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($room['name']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($room['description']) ?></p>
                                    <p><strong>Check-in:</strong> <?= htmlspecialchars($arrival) ?></p>
                                    <p><strong>Check-out:</strong> <?= htmlspecialchars($departure) ?></p>
                                    <p><strong>Price:</strong> RM <?= number_format($room['price'], 2) ?> per night</p>
                                    <p><strong>Quantity:</strong> <?= $room['quantity'] ?> rooms selected</p>
                                    <?php if (!empty($room['id'])): ?>
                                        <input type="hidden" name="rooms[<?= (int)$room['id'] ?>]" value="<?= (int)$room['quantity'] ?>">
                                    <?php else: ?>
                                        <?php file_put_contents('debug.txt', "❌ Missing room ID in booking-confirmation view.\n", FILE_APPEND); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Personal Details Section -->
        <div class="mb-4">
            <h4>Your Details</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name <span style="color: #ff0909">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number <span style="color: #ff0909">*</span></label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address <span style="color: #ff0909">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Payment Details Section -->
        <div class="mb-4">
            <h4>Payment Details</h4>
            <div class="form-group">
                <label for="payment_method" class="form-label">Payment Method <span style="color: #ff0909">*</span></label>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="qr">QR Payment</option>
                </select>
            </div>

            <!-- Card payment fields (shown when 'card' is selected) -->
            <div id="card_payment_details" class="payment-method-fields">
                <div class="row">
                    <!-- Card Number -->
                    <div class="col-md-6 mb-3">
                        <label for="card_number" class="form-label">Card Number </label>
                        <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" />
                    </div>

                    <!-- Expiry Date -->
                    <div class="col-md-3 mb-3">
                        <label for="expiry_date" class="form-label">Expiry Date </label>
                        <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" />
                    </div>

                    <!-- CVV -->
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

        <!-- Payment Information (Optional Field) -->
        <div class="form-group">
            <label for="payment_details" class="form-label">Payment Information (Optional)</label>
            <textarea id="payment_details" name="payment_details" class="form-control" placeholder="Additional payment details or instructions (optional)"></textarea>
        </div>

        <!-- Total Price Summary -->
        <div class="mb-4">
            <h4>Total Price</h4>
            <p><strong>Total Amount:</strong> RM <?= number_format($totalAmount, 2) ?></p>
            <input type="hidden" name="total_amount" value="<?= htmlspecialchars($totalAmount) ?>">
        </div>

        <!-- Action Buttons -->
        <div class="mb-4">
            <button type="submit" class="btn btn-success">Confirm Booking</button>
            <a href="<?= APP_URL ?>/rooms" class="btn btn-secondary">Back to Rooms</a>
        </div>
    </form>
</div>
