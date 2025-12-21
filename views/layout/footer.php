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
                    <li> <i class="fas fa-envelope" aria-hidden="true"></i><a href="#"> cherating_guesthouse@gmail.com</a></li>
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