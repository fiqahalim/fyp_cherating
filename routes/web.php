<?php

// Sanity check route
Route::get('/ping', function () {
    echo "Pong! Routing is working ✅";
});

// Home
Route::get('/', 'Home@index');
Route::match(['GET', 'POST'], '/rooms', 'Home@rooms');
Route::get('/about', 'Home@about');
Route::get('/contact', 'Home@contact');
Route::post('/contact-submit', 'Home@handleContactForm');
Route::post('/booking-confirmation', 'Home@bookingConfirmation');
Route::post('/confirm-booking', 'Home@confirmBooking');
Route::get('/confirmation-done/{booking_id}', 'Home@confirmationDone');
Route::get('/download-invoice/{booking_id}', 'Home@downloadInvoice');

// Grouped routes for /admin
Route::group('/admin', function () {
    // Rooms
    Route::get('/rooms', 'Room@index');
    Route::match(['GET', 'POST'], '/rooms/create', 'Room@createOrUpdate');
    Route::match(['GET', 'POST'], '/rooms/edit/{id}', 'Room@createOrUpdate');
    Route::get('/room/view/{id}', 'Room@view');

    // Bookings
    Route::get('/bookings', 'Booking@index');
    Route::match(['GET', 'POST'], '/bookings/create', 'Booking@createOrUpdate');
    Route::match(['GET', 'POST'], '/bookings/edit/{id}', 'Booking@createOrUpdate');
    Route::get('/bookings/view/{id}', 'Booking@viewBooking');
    Route::get('/bookings/{id}/delete', 'Booking@delete');

    // Messages
    Route::get('/messages', 'Message@index');
    Route::get('/messages/view/{id}', 'Message@viewMessage');

    // Admin auth
    Route::get('/login', 'Admin@login');
    Route::get('/profile', 'Admin@profile');
    Route::post('/updateProfile', 'Admin@updateProfile');
    Route::post('/authenticate', 'Admin@authenticate');
    Route::get('/dashboard', 'Admin@dashboard');
    Route::get('/logout', 'Admin@logout');
});