<?php
// $base_url = '/fyp_cherating';
$base_url = "http://localhost:8000/FYP/fyp_cherating"; //for macbook
?>

<div class="about">
    <div class="container">
        <!-- Page Title -->
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage">
                    <h2>About Cherating Guest House</h2>
                    <p>Your cozy retreat by the beach in Kuantan. Designed for comfort and convenience, our guest house offers a relaxing stay for families, couples, and solo travelers alike.</p>
                </div>
            </div>
        </div>

        <!-- Section 1: Comfortable Accommodations -->
        <div class="row align-items-center mb-5 animate-slide-in">
            <div class="col-md-6">
                <img src="<?= $base_url ?>/assets/images/AboutUsPage/Picture1.png" class="img-fluid rounded" alt="Comfortable Rooms">
            </div>
            <div class="col-md-6">
                <h3>Comfortable Accommodations</h3>
                <p>Our rooms feature air-conditioning, private bathrooms, balconies, and cozy seating areas. Perfect for families, couples, or solo travelers, every room is designed to make you feel right at home.</p>
            </div>
        </div>

        <!-- Section 2: Essential Facilities -->
        <div class="row align-items-center mb-5 animate-slide-in">
            <div class="col-md-6 order-md-2">
                <img src="<?= $base_url ?>/assets/images/AboutUsPage/Picture2.png" class="img-fluid rounded" alt="Facilities">
            </div>
            <div class="col-md-6 order-md-1">
                <h3>Essential Facilities</h3>
                <p>Enjoy complimentary Wi-Fi, a serene garden, free private parking, and a private entrance. We provide everything you need for a seamless stay.</p>
            </div>
        </div>

        <!-- Section 3: Prime Location -->
        <div class="row align-items-center mb-5 animate-slide-in">
            <div class="col-md-6">
                <img src="<?= $base_url ?>/assets/images/AboutUsPage/Picture3.png" class="img-fluid rounded" alt="Prime Location">
            </div>
            <div class="col-md-6">
                <h3>Prime Location</h3>
                <p>Just a short walk from Cherating Beach, Limbong Art, and local attractions like the Cherating Turtle Sanctuary, Cherating Guest House is the perfect base for exploring the area.</p>
            </div>
        </div>

        <!-- Section 4: Guest Favorites -->
        <div class="row align-items-center mb-5 animate-slide-in">
            <div class="col-md-6 order-md-2">
                <img src="<?= $base_url ?>/assets/images/AboutUsPage/Picture4.png" class="img-fluid rounded" alt="Guest Favorites">
            </div>
            <div class="col-md-6 order-md-1">
                <h3>Guest Favorites</h3>
                <p>Visitors love our beach access, convenient location, and comfortable bathrooms. Couples especially appreciate our charming environment, making it perfect for romantic getaways.</p>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="about-section row animate-slide-in">
            <div class="col-12">
                <h3>Nearby Amenities & Attractions</h3>
            </div>

            <!-- Column 1: Restaurants & Cafes -->
            <div class="col-md-3 d-flex align-items-start mb-3">
                <!-- Fork & Knife Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28" width="24px" height="24" fill="currentColor" class="me-2"><path d="M5.999.75v22.5a.75.75 0 0 0 1.5 0V.75a.75.75 0 0 0-1.5 0m3 0V7.5a2.26 2.26 0 0 1-2.252 2.25 2.26 2.26 0 0 1-2.248-2.252V.75a.75.75 0 0 0-1.5 0V7.5a3.76 3.76 0 0 0 3.748 3.75 3.76 3.76 0 0 0 3.752-3.748V.75a.75.75 0 0 0-1.5 0m6.75 15.75h3c1.183.046 2.203-.9 2.25-2.111a2 2 0 0 0 0-.168c-.25-6.672-.828-9.78-3.231-13.533a1.508 1.508 0 0 0-2.77.81V23.25a.75.75 0 0 0 1.5 0V1.503c0 .003.001 0 .003 0l.008.002c2.21 3.45 2.75 6.354 2.99 12.773v.053a.696.696 0 0 1-.721.67L15.749 15a.75.75 0 0 0 0 1.5"></path></svg>
                <div>
                    <h5>Restaurants & Cafes</h5>
                    <ul>
                        <li>Cafe/Bar Cherating Beach</li>
                        <li>Restoran Duyong</li>
                        <li>Restoran Makanan Laut Intan</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-start mb-3">
                <div>
                    <ul class="mt-4">
                        <li>350 m</li>
                        <li>700 m</li>
                        <li>850 m</li>
                    </ul>
                </div>
            </div>

            <!-- Column 2: Beaches -->
            <div class="col-md-3 d-flex align-items-start mb-3">
                <!-- Beach Umbrella Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="me-2" viewBox="0 0 28 28">
                    <path d="m.153 22.237.85 1.117c.634.76 1.724.856 2.456.244q.117-.099.216-.217l.944-1.132a.228.228 0 0 1 .349.001l.944 1.13a1.728 1.728 0 0 0 2.651.001l.944-1.132a.228.228 0 0 1 .349.001l.95 1.132a1.728 1.728 0 0 0 2.65 0l.942-1.133a.228.228 0 0 1 .349.001l.942 1.13a1.728 1.728 0 0 0 2.651.001l.944-1.132a.228.228 0 0 1 .349.001l.94 1.13a1.728 1.728 0 0 0 2.652.001l.585-.7a.75.75 0 1 0-1.15-.962l-.585.7a.228.228 0 0 1-.35 0l-.94-1.13a1.728 1.728 0 0 0-2.652-.001l-.944 1.132a.228.228 0 0 1-.349-.001l-.942-1.13a1.728 1.728 0 0 0-2.651-.001l-.943 1.132a.228.228 0 0 1-.348-.001l-.95-1.132a1.726 1.726 0 0 0-2.65 0l-.944 1.133a.228.228 0 0 1-.349-.001l-.944-1.13a1.728 1.728 0 0 0-2.65 0l-.945 1.13a.228.228 0 0 1-.349-.001l-.828-1.09a.75.75 0 1 0-1.194.91zm11.335-2.68A7.16 7.16 0 0 1 15.77 18h7.481a.75.75 0 0 0 0-1.5h-7.5a8.67 8.67 0 0 0-5.196 1.884.75.75 0 1 0 .934 1.174zM22.285 7.969a1.73 1.73 0 0 0 .781-2.711C19.43.713 12.8-.022 8.256 3.614a10.54 10.54 0 0 0-3.952 8.171A1.73 1.73 0 0 0 6.6 13.427l15.684-5.459zm-.494-1.416L6.107 12.01a.23.23 0 0 1-.304-.218 9.036 9.036 0 0 1 16.09-5.599.228.228 0 0 1-.102.359zm-9.459-4.2L11.69.504a.75.75 0 1 0-1.416.492l.643 1.848a.75.75 0 1 0 1.416-.492zm1.156 7.883 2.527 7.262a.75.75 0 1 0 1.416-.494l-2.527-7.26a.75.75 0 1 0-1.416.492"/>
                </svg>
                <div>
                    <h5>Beaches</h5>
                    <ul>
                        <li>Cherating Beach</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-start mb-3">
                <div>
                    <ul class="mt-4">
                        <li>400 m</li>
                    </ul>
                </div>
            </div>

            <!-- Column 3: Closest Airport -->
            <div class="col-md-3 d-flex align-items-start mb-3">
                <!-- Airplane Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="me-2" viewBox="0 0 28 28">
                    <path d="M18.33 3.57 5.7 9.955l.79.07-1.96-1.478a.75.75 0 0 0-.753-.087l-2.1.925C.73 9.856.359 10.967.817 11.88c.11.22.263.417.45.577l3.997 3.402a2.94 2.94 0 0 0 3.22.4l3.62-1.8-1.084-.671v5.587a1.833 1.833 0 0 0 2.654 1.657l1.88-.932a1.85 1.85 0 0 0 .975-1.226l1.87-7.839-.396.498 3.441-1.707a3.494 3.494 0 1 0-3.11-6.259zm.672 1.342a1.994 1.994 0 0 1 1.775 3.571l-3.44 1.707a.75.75 0 0 0-.396.498l-1.87 7.838a.35.35 0 0 1-.185.232l-1.88.932a.335.335 0 0 1-.486-.304V13.79a.75.75 0 0 0-1.084-.672l-3.62 1.8a1.44 1.44 0 0 1-1.578-.197l-3.997-3.402a.35.35 0 0 1 .073-.577l2.067-.91-.754-.087 1.96 1.478a.75.75 0 0 0 .79.07l12.63-6.383zm-3.272.319-4.46-2.26a1.85 1.85 0 0 0-1.656-.001l-1.846.912a1.85 1.85 0 0 0-.295 3.128l2.573 1.955a.75.75 0 1 0 .908-1.194L8.38 5.816a.35.35 0 0 1 .055-.591l1.845-.912a.35.35 0 0 1 .315 0l4.456 2.256a.75.75 0 0 0 .678-1.338z"/>
                </svg>
                <div>
                    <h5>Closest Airport</h5>
                    <ul>
                        <li>Sultan Haji Ahmad Shah Airport</li>
                    </ul>
                </div>
            </div>
                <div class="col-md-1 d-flex align-items-start mb-3">
                <div>
                    <ul class="mt-4">
                        <li>55 km</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Scroll Animation JS -->
<script>
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.bottom >= 0
        );
    }

    const animatedElements = document.querySelectorAll('.animate-slide-in');

    function checkAnimations() {
        animatedElements.forEach(el => {
            if (isInViewport(el)) {
                el.classList.add('visible');
            }
        });
    }

    window.addEventListener('scroll', checkAnimations);
    window.addEventListener('load', checkAnimations);
</script>