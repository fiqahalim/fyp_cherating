<?php

class AdminController extends Controller
{
    // Declare the model property
    private $bookingModel, $contactModel, $roomModel, $customerModel, $adminModel;

    // Constructor to initialize the model
    public function __construct()
    {
        // Initialize the model (only once)
        $this->bookingModel = $this->model('BookingModel');
        $this->contactModel = $this->model('ContactModel');
        $this->roomModel = $this->model('RoomModel');
        $this->customerModel = $this->model('CustomerModel');
        $this->adminModel = $this->model('AdminModel');

        $this->db = Database::getInstance()->getConnection();
    }

    public function getUnpaidBookingsJson()
    {
        $unpaid = $this->bookingModel->getUnpaidBookingsWithPaymentStatus(10);
        header('Content-Type: application/json');
        echo json_encode($unpaid);
        exit;
    }

    public function getGlobalNotifications()
    {
        // 1. New Bookings (Today)
        $newBookings = $this->db->query("SELECT id, booking_ref_no FROM bookings WHERE DATE(created_at) = CURDATE()")->fetchAll();
        
        // 2. QR Payments waiting for verification
        $pendingQR = $this->db->query("SELECT b.booking_ref_no, p.id FROM payments p 
            JOIN bookings b ON p.booking_id = b.id 
            WHERE p.payment_method = 'qr' AND p.verified = 'pending'")->fetchAll();

        // 3. Cancellations (> 5 days before check-in)
        $cancellations = $this->db->query("SELECT booking_ref_no FROM bookings 
            WHERE status = 'cancelled' 
            AND DATEDIFF(check_in, updated_at) >= 5")->fetchAll();

        header('Content-Type: application/json');
        echo json_encode([
            'new_bookings' => $newBookings,
            'pending_qr' => $pendingQR,
            'cancellations' => $cancellations,
            'total' => count($newBookings) + count($pendingQR) + count($cancellations)
        ]);
        exit;
    }
}