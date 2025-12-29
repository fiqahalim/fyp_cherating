<?php

class AdminController extends Controller
{
    // Declare the model property
    private $db, $bookingModel, $contactModel, $roomModel, $customerModel, $adminModel;

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
        header('Content-Type: application/json');
        try {
            // 1. New Bookings
            $newBookings = $this->db->query("SELECT id, booking_ref_no FROM bookings 
                WHERE DATE(created_at) = CURDATE() 
                ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
            
            // 2. QR Payments
            $pendingQR = $this->db->query("SELECT b.id, b.booking_ref_no FROM payments p 
                JOIN bookings b ON p.booking_id = b.id 
                WHERE p.payment_method = 'qr' AND p.verified = 'pending'")->fetchAll(PDO::FETCH_ASSOC);

            // 3. Cancellations
            $cancellations = $this->db->query("SELECT id, booking_ref_no FROM bookings 
                WHERE status = 'cancelled' 
                AND DATEDIFF(check_in, updated_at) >= 5 
                ORDER BY updated_at DESC")->fetchAll(PDO::FETCH_ASSOC);

            // 4. Messages
            $messages = $this->db->query("SELECT id, name, message, created_at FROM contacts 
                ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'new_bookings' => $newBookings,
                'pending_qr' => $pendingQR,
                'cancellations' => $cancellations,
                'messages' => $messages,
                'total' => count($newBookings) + count($pendingQR) + count($cancellations)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}