<?php
// $base_url = '/fyp_cherating';
$base_url = "http://localhost:8000/FYP/fyp_cherating"; //for macbook
$isLoggedIn = !empty($_SESSION['is_logged_in']);
$isAdmin = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'admin';
$isCustomer = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'customer';
?>

<?php if (!$isAdmin): ?>
<footer>
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-10">
                    <h3>Contact US</h3>
                    <ul class="conta">
                    <li><i class="fas fa-map-marker" aria-hidden="true"></i> 4/1000 Kampung Budaya, Jalan Kampung Cherating Lama, 26080 Kuantan, Pahang</li>
                    <li><i class="fas fa-mobile" aria-hidden="true"></i> +6011 1103 4533</li>
                    <li> <i class="fas fa-envelope" aria-hidden="true"></i><a href="#"> demo@gmail.com</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h3>Menu Link</h3>
                    <ul class="link_menu">
                    <li><a class="nav-link" href="<?= $base_url ?>/">Home</a></li>
                    <li><a class="nav-link" href="<?= $base_url ?>/about">About</a></li>
                    <li><a class="nav-link" href="<?= $base_url ?>/rooms">Our Rooms</a></li>
                    <li><a class="nav-link" href="<?= $base_url ?>/contact">Contact Us</a></li>
                    </ul>
                </div>
                <!-- <div class="col-md-4">
                    <h3>Our Media Social</h3>
                    <ul class="social_icon">
                    <li><a href="#"><i class="fab fa-facebook" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fab fa-linkedin" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fab fa-youtube-play" aria-hidden="true"></i></a></li>
                    </ul>
                </div> -->
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                    <p>
                    © 2025 All Rights Reserved. Design by <a href="#"> Amelia</a>
                    </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>
</body>
</html>

<!-- Javascript files-->
<script src="<?= $base_url ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= $base_url ?>/vendor/bootstrap/js/bootstrap.min.js"></script>
<!-- Other Plugins -->
<script src="<?= $base_url ?>/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?= $base_url ?>/assets/js/custom.js"></script>
<script>
    // Auto-dismiss flash alerts with smooth fade out
    document.addEventListener("DOMContentLoaded", function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function () {
                // Add fade-out class
                alert.classList.add('fade-out');

                // Wait for CSS transition, then close it properly
                setTimeout(function() {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 800); // match transition duration
            }, 6000); // 6s visible before fading
        });
    });

    // payment method
    document.getElementById('payment_method').addEventListener('change', function () {
        var paymentMethod = this.value;

        // Hide all payment method fields initially
        document.querySelectorAll('.payment-method-fields').forEach(function (field) {
            field.style.display = 'none';
        });

        // show the selected payment method's fields
        if (paymentMethod === 'card') {
            document.getElementById('card_payment_details').style.display = 'block';
        } else if (paymentMethod === 'paypal') {
            document.getElementById('paypal_payment_details').style.display = 'block';
        } else if (paymentMethod === 'qr') {
            document.getElementById('qr_payment_details').style.display = 'block';
            generateQrCode(); // Function to generate QR code
        }
    });

    // function to generate the QR Code for the payment
    function generateQrCode() {
        var qrCodeContainer = document.getElementById('qr_code_container');

        // Example Touch 'n Go QR URL. Replace with actual payment URL if you integrate API.
        // var qrCodeUrl = "https://www.tngdigital.com.my/pay?amount=" + <?= $totalAmount ?> + "&merchant_id=YOUR_MERCHANT_ID&order_id=<?= uniqid() ?>";

        // Testing TNG URL
        const qrCodeUrl = "TNG Payment - Amount: RM " + <?= json_encode($totalAmount) ?>;

        // Use a library like PHP QR Code to generate the QR code on the backend, or JavaScript library on the frontend.
        qrCodeContainer.innerHTML = '<img src="https://api.qrserver.com/v1/create-qr-code/?data=' + encodeURIComponent(qrCodeUrl) + '&size=200x200" alt="QR Code" />';
    }

    // Auto format card number (e.g., 1234 5678 9012 3456)
    document.getElementById('card_number').addEventListener('input', function (e) {
        let input = this.value.replace(/\D/g, '').substring(0, 16); // Digits only, max 16
        let groups = input.match(/.{1,4}/g);
        this.value = groups ? groups.join(' ') : '';
    });

    // Auto-format expiry date as MM/YY
    document.getElementById('expiry_date').addEventListener('input', function (e) {
        let input = this.value.replace(/\D/g, '').substring(0, 4); // Only digits, MMYY
        if (input.length >= 3) {
            this.value = input.substring(0, 2) + '/' + input.substring(2, 4);
        } else {
            this.value = input;
        }
    });
</script>