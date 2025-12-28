<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// $base_url = '/fyp_cherating';
$base_url = "http://localhost:8000/FYP/fyp_cherating"; //for macbook
$isLoggedIn = !empty($_SESSION['is_logged_in']);
$isAdmin = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'admin';
$isCustomer = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'customer';

// Detect current page
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Amelia Natasya">
    <title>Admin | Cherating Guest House</title>
    <!-- Admin Specific Styles -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/sb-admin-2.css">
    <link href="<?= $base_url ?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        let lastNotificationCount = 0;
        const EXPIRATION_DAYS = 30;

        // 1. Enhanced: Get read IDs and PURGE expired ones
        function getReadIds() {
            let storage = JSON.parse(localStorage.getItem('read_notifications_v2') || '[]');
            const now = new Date().getTime();
            const expirationMs = EXPIRATION_DAYS * 24 * 60 * 60 * 1000;

            // Only keep items that haven't expired
            const validItems = storage.filter(item => (now - item.time) < expirationMs);
            
            // If we removed expired items, update the storage
            if (validItems.length !== storage.length) {
                localStorage.setItem('read_notifications_v2', JSON.stringify(validItems));
            }

            return validItems.map(item => item.id);
        }

        // 2. Enhanced: Save ID with a timestamp
        function markAsRead(uniqueId) {
            let storage = JSON.parse(localStorage.getItem('read_notifications_v2') || '[]');
            let ids = storage.map(item => item.id);

            if (!ids.includes(uniqueId)) {
                storage.push({
                    id: uniqueId,
                    time: new Date().getTime()
                });
                localStorage.setItem('read_notifications_v2', JSON.stringify(storage));
            }
            checkNotifications();
        }

        // 3. Clear All Function
        function clearAllNotifications() {
            fetch('<?= $base_url ?>/admin/getGlobalNotifications')
                .then(response => response.json())
                .then(data => {
                    let storage = JSON.parse(localStorage.getItem('read_notifications_v2') || '[]');
                    let ids = storage.map(item => item.id);
                    const now = new Date().getTime();
                    
                    const allItems = [
                        ...data.pending_qr.map(i => 'qr_'+i.id), 
                        ...data.new_bookings.map(i => 'nb_'+i.id), 
                        ...data.cancellations.map(i => 'can_'+i.id),
                        ...data.messages.map(i => 'msg_'+i.id) // Added messages here
                    ];

                    allItems.forEach(uniqueId => {
                        if (!ids.includes(uniqueId)) {
                            storage.push({ id: uniqueId, time: now });
                        }
                    });
                    
                    localStorage.setItem('read_notifications_v2', JSON.stringify(storage));
                    checkNotifications();
                });
        }

        function checkNotifications() {
            fetch('<?= $base_url ?>/admin/getGlobalNotifications')
                .then(response => response.json())
                .then(data => {
                    // Define all containers
                    const container = document.getElementById('notification-items-container');
                    const messageContainer = document.getElementById('message-items-container'); // Defined here
                    const counter = document.getElementById('global-nav-count');
                    const msgCounter = document.getElementById('global-msg-count'); // Unique name
                    const sound = document.getElementById('notification-sound');
                    const clearAllBtn = document.getElementById('clear-all-link');
                    
                    const readIds = getReadIds();

                    // Filters
                    const filteredQR = data.pending_qr.filter(item => !readIds.includes('qr_' + item.id));
                    const filteredNew = data.new_bookings.filter(item => !readIds.includes('nb_' + item.id));
                    const filteredCan = data.cancellations.filter(item => !readIds.includes('can_' + item.id));
                    const filteredMessages = data.messages.filter(m => !readIds.includes('msg_' + m.id));

                    const unreadBookingTotal = filteredQR.length + filteredNew.length + filteredCan.length;

                    // Sound logic (based on bookings)
                    if (unreadBookingTotal > lastNotificationCount) {
                        sound.play().catch(e => {});
                    }
                    lastNotificationCount = unreadBookingTotal;

                    // Update Badges
                    counter.innerText = unreadBookingTotal > 0 ? unreadBookingTotal : '';
                    if(msgCounter) {
                        msgCounter.innerText = filteredMessages.length > 0 ? filteredMessages.length : '';
                    }

                    if (clearAllBtn) {
                        // Button shows if there is ANY unread content (bookings OR messages)
                        clearAllBtn.style.display = (unreadBookingTotal + filteredMessages.length) > 0 ? 'block' : 'none';
                    }

                    // Render Bookings
                    let html = '';
                    filteredQR.forEach(item => {
                        html += `<a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/admin/payments/verify/${item.id}" onclick="markAsRead('qr_${item.id}')">
                            <div class="mr-3"><div class="icon-circle bg-warning"><i class="fas fa-qrcode text-white"></i></div></div>
                            <div><div class="small text-gray-500">QR Payment</div>Ref: ${item.booking_ref_no}</div>
                        </a>`;
                    });
                    filteredNew.forEach(item => {
                        html += `<a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/admin/bookings/view/${item.id}" onclick="markAsRead('nb_${item.id}')">
                            <div class="mr-3"><div class="icon-circle bg-primary"><i class="fas fa-calendar-plus text-white"></i></div></div>
                            <div><div class="small text-gray-500">New Booking</div>Ref: ${item.booking_ref_no}</div>
                        </a>`;
                    });
                    filteredCan.forEach(item => {
                        html += `<a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/admin/bookings/view/${item.id}" onclick="markAsRead('can_${item.id}')">
                            <div class="mr-3"><div class="icon-circle bg-danger"><i class="fas fa-user-times text-white"></i></div></div>
                            <div><div class="small text-gray-500">Cancellation</div>Ref: ${item.booking_ref_no}</div>
                        </a>`;
                    });
                    if(unreadBookingTotal === 0) {
                        html = '<a class="dropdown-item text-center small text-gray-500" href="#">No new alerts</a>';
                    }
                    container.innerHTML = html;

                    // Render Messages
                    let msgHtml = '';
                    filteredMessages.forEach(msg => {
                        const shortMsg = msg.message.length > 40 ? msg.message.substring(0, 40) + '...' : msg.message;
                        msgHtml += `
                        <a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/admin/messages/view/${msg.id}" onclick="markAsRead('msg_${msg.id}')">
                            <div class="dropdown-list-image mr-3">
                                <img class="rounded-circle" src="<?= $base_url ?>/assets/images/undraw_profile_1.svg" alt="...">
                                <div class="status-indicator bg-success"></div>
                            </div>
                            <div class="font-weight-bold">
                                <div class="text-truncate">${shortMsg}</div>
                                <div class="small text-gray-500">${msg.name}</div>
                            </div>
                        </a>`;
                    });
                    if (filteredMessages.length === 0) {
                        msgHtml = '<a class="dropdown-item text-center small text-gray-500" href="#">No new messages</a>';
                    }
                    if(messageContainer) messageContainer.innerHTML = msgHtml;
                });
        }
        setInterval(checkNotifications, 15000);
        document.addEventListener('DOMContentLoaded', checkNotifications);
    </script>
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#" onclick="event.preventDefault();">
                <div class="sidebar-brand-icon rotate-n-15">
                    <!-- <i class="fas fa-laugh-wink"></i> -->
                    <img src="<?= $base_url ?>/assets/images/Cherating_Indah_Logo.png" alt="Cherating Guest House Logo" />
                </div>
                <!-- <div class="sidebar-brand-text mx-3">Cherating Guest House</div> -->
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?= $base_url ?>/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>DASHBOARD</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <!-- Nav Menu -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/bookings">
                    <i class="fas fa-book"></i><span>BOOKINGS</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/payments">
                    <i class="fas fa-money"></i><span>PAYMENTS</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/rooms">
                    <i class="fas fa-hotel"></i><span>ROOMS</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/room-virtuals">
                    <i class="fas fa-magnifying-glass"></i><span>360° VIRTUAL TOUR</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/messages">
                    <i class="fas fa-comment"></i><span>MESSAGES</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/auth/logout">
                    <i class="fas fa-sign-out-alt"></i>
                        Logout
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter" id="global-nav-count">0</span>
                            </a>
                            
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    Alerts Center
                                    <span id="clear-all-link" style="cursor:pointer; text-transform: none; font-weight: normal; font-size: 0.8rem; display:none;" onclick="clearAllNotifications()">
                                        Mark all read
                                    </span>
                                </h6>
                                
                                <div id="notification-items-container"></div>
                                
                                <a class="dropdown-item text-center small text-gray-500" href="<?= $base_url ?>/admin/bookings">View All Bookings</a>
                            </div>
                        </li>

                        <audio id="notification-sound" preload="auto">
                            <source src="<?= $base_url ?>/assets/audio/ding.mp3" type="audio/mpeg">
                        </audio>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-danger badge-counter" id="global-msg-count"></span>
                            </a>
                            
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>

                                <div id="message-items-container">
                                    <a class="dropdown-item text-center small text-gray-500" href="#">Loading messages...</a>
                                </div>

                                <a class="dropdown-item text-center small text-gray-500" href="<?= $base_url ?>/admin/messages">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?= $_SESSION['full_name'] ?? 'Admin' ?>
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="<?= $base_url ?>/assets/images/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= $base_url ?>/profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal" onclick="event.preventDefault();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

