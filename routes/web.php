<?php

// Sanity check route
Route::get('/ping', function () {
    echo "Pong! Routing is working ✅";
});

// ------------------------
// Home / Public Routes
// ------------------------
Route::get('/', 'Home@index');
Route::match(['GET', 'POST'], '/rooms', 'Home@rooms');
Route::get('/about', 'Home@about');
Route::get('/contact', 'Home@contact');
Route::post('/contact-submit', 'Home@handleContactForm');
Route::post('/booking-confirmation', 'Home@bookingConfirmation');
Route::post('/confirm-booking', 'Home@confirmBooking');
Route::get('/finalize-booking', 'Home@finalizeBooking');
Route::get('/confirmation-done/{booking_id}', 'Home@confirmationDone');
Route::get('/download-invoice/{booking_id}', 'Home@downloadInvoice');
Route::post('/check-customer-availability', 'Home@checkCustomerAvailability');

// ------------------------
// Unified Authentication (Admin + Customer)
// ------------------------
Route::group('/auth', function () {
    Route::get('/login', 'Auth@login');
    Route::post('/login', 'Auth@authenticate');
    Route::get('/register', 'Auth@register');
    Route::post('/register', 'Auth@registerProcess'); 
    Route::get('/logout', 'Auth@logout');
    Route::get('/verify', 'Auth@verify');
    Route::post('/verify', 'Auth@verify');
    // forgot & reset password
    Route::get('/forgot-password', 'Auth@forgotPassword');
    Route::post('/forgot-password', 'Auth@forgotPasswordProcess');
    Route::get('/reset-password', 'Auth@resetPassword');
    Route::post('/reset-password', 'Auth@resetPasswordProcess');
});
Route::get('/dashboard', 'Auth@dashboard');
Route::get('/profile', 'Auth@profile');
Route::post('/updateProfile', 'Auth@updateProfile');

// ------------------------
// Admin Routes
// ------------------------
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

    // Payments

    // Messages
    Route::get('/messages', 'Message@index');
    Route::get('/messages/view/{id}', 'Message@viewMessage');
});