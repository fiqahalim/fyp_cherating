<?php

require_once 'helpers/WhatsAppHelper.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class HomeController extends Controller
{
    // Declare the model property
    private $bookingModel, $contactModel, $roomModel, $customerModel;

    // Constructor to initialize the model
    public function __construct()
    {
        // Initialize the model (only once)
        $this->bookingModel = $this->model('BookingModel');
        $this->roomModel = $this->model('RoomModel');
        $this->contactModel = $this->model('ContactModel');
        $this->customerModel = $this->model('CustomerModel');
    }

    // Home Page
    public function index()
    {
        $this->view("home/index", [
            "title" => "Welcome to " . APP_NAME
        ]);
    }

    // About Page
    public function about()
    {
        $this->view("home/about", [
            "title" => "About Us - " . APP_NAME
        ]);
    }

    // Rooms page after selecting dates, show availability and prices
    public function rooms()
    {
        // Get dates from POST (form submission) or GET (filter links)
        $arrival = $_POST['arrival_date'] ?? $_GET['arrival_date'] ?? null;
        $departure = $_POST['departure_date'] ?? $_GET['departure_date'] ?? null;

        // Check if the arrival date is before the departure date
        if ($arrival && $departure && $arrival > $departure) {
            Flash::set('error', 'Departure date must be after arrival date.');
            header('Location: ' . APP_URL);
            exit;
        }

        // Fetch the price range and room type filters if provided
        $priceRange = $_GET['price_range'] ?? null;
        $roomType = $_GET['room_type'] ?? null;

        // Get all rooms
        if ($arrival && $departure) {
            // Get all rooms with their availability counts based on the dates
            $rooms = $this->roomModel->getAvailableRoomsWithCounts($arrival, $departure);
        } else {
            // get all rooms without checking availability
            $rooms = $this->roomModel->getAllRooms();
            // set availability as null for display purposes
            foreach ($rooms as &$room) {
                $room['available'] = null; // availability unknown
            }
        }

        // Filter rooms by price range if provided
        if ($priceRange) {
            $priceParts = explode('-', $priceRange);
            $minPrice = $priceParts[0];
            $maxPrice = isset($priceParts[1]) ? $priceParts[1] : 999999;

            $rooms = array_filter($rooms, function($room) use ($minPrice, $maxPrice) {
                return $room['price'] >= $minPrice && $room['price'] <= $maxPrice;
            });
        }

        // Filter rooms by room type if provided
        if ($roomType) {
            $rooms = array_filter($rooms, function($room) use ($roomType) {
                return $room['id'] == $roomType;
            });
        }

        // Render the view with the filtered rooms
        $this->view('home/rooms', ['rooms' => $rooms, 'arrival' => $arrival, 'departure' => $departure]);
    }

    public function contact()
    {
        $this->view("home/contact", [
            "title" => "Contact Us - " . APP_NAME
        ]);
    }

    // Booking confirmation form (after selecting rooms and quantities)
    public function bookingConfirmation()
    {
        $arrival = $_POST['arrival_date'] ?? null;
        $departure = $_POST['departure_date'] ?? null;
        $selectedRooms = $_POST['rooms'] ?? [];
        $totalAmount = 0;

        if (!$arrival || !$departure || empty($selectedRooms)) {
            Flash::set('error', 'Please select dates and rooms');
            header('Location: ' . APP_URL);
            exit;
        }

        $rooms = $this->roomModel->getRoomsByIds(array_keys($selectedRooms));

        // Add quantity selected to each room
        foreach ($rooms as &$room) {
            $room['quantity'] = (int)($selectedRooms[$room['id']] ?? 0);

            // Add the room's price multiplied by the selected quantity to the total amount
            if ($room['quantity'] > 0) {
                $totalAmount += $room['price'] * $room['quantity'];
            }
        }

        // Save selected rooms and booking details to session
        $_SESSION['selected_rooms'] = $selectedRooms; // e.g. [2 => 1, 5 => 2]
        $_SESSION['check_in'] = $arrival;
        $_SESSION['check_out'] = $departure;
        $_SESSION['total_amount'] = $totalAmount;

        $this->view('home/booking-confirmation', [
            'arrival' => $arrival,
            'departure' => $departure,
            'rooms' => $rooms,
            'totalAmount' => $totalAmount,
        ]);
    }

    // Save booking to DB
    public function confirmBooking()
    {
        file_put_contents('debug.txt', 'confirmBooking reached' . PHP_EOL, FILE_APPEND);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            file_put_contents('debug.txt', 'Not a POST request' . PHP_EOL, FILE_APPEND);
            header('Location: ' . APP_URL);
            exit;
        }

        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        $check_in = $_POST['check_in'] ?? '';
        $check_out = $_POST['check_out'] ?? '';
        $rooms = $_POST['rooms'] ?? []; // room_id => quantity
        $payment_method = $_POST['payment_method'] ?? '';
        $payment_details = $_POST['payment_details'] ?? '';
        $totalAmount = $_POST['total_amount'] ?? 0.0;

        $payment_details = '';
        if ($payment_method === 'card') {
            $payment_details = 'Card ending in ' . substr($_POST['card_number'] ?? '', -4);
        } elseif ($payment_method === 'paypal') {
            $payment_details = 'PayPal Email: ' . ($_POST['paypal_email'] ?? 'N/A');
        }

        // Get booking details from session
        // $check_in = $_SESSION['check_in'] ?? '';
        // $check_out = $_SESSION['check_out'] ?? '';
        // $rooms = $_SESSION['selected_rooms'] ?? [];

        // Validate inputs
        if (!$name || !$phone || !$email || !$check_in || !$check_out || empty($rooms)) {
            Flash::set('error', 'Please fill in all required fields.');
            header('Location: ' . APP_URL);
            exit;
        }

        $customerId = $this->customerModel->findOrCreateGuest($name, $phone, $email, $username, $password);

        if (!$customerId) {
            file_put_contents('debug.txt', 'Customer identification/creation failed for email: ' . $email . PHP_EOL, FILE_APPEND);
            Flash::set('error', 'Failed to save customer details. Please try again.');
            header('Location: ' . APP_URL);
            exit;
        }

        // If payment method is QR, generate the QR code URL
        if ($payment_method === 'qr') {
            // Generate Touch 'n Go payment URL (this is a simulated URL for demonstration)
            $merchantId = 'YOUR_MERCHANT_ID'; // Replace with your actual merchant ID
            $orderId = uniqid();  // Generate a unique order ID
            $paymentUrl = "https://www.tngdigital.com.my/pay?amount=" . $totalAmount . "&merchant_id=" . $merchantId . "&order_id=" . $orderId;

            // Pass the payment URL as part of the payment details
            $payment_details = "QR Payment - Amount: RM" . number_format($totalAmount, 2) . ", Order ID: " . $orderId;
        }

        // Save each room booking record
        $booking_id = $this->bookingModel->createBooking(
            $customerId, 
            $check_in, 
            $check_out, 
            $rooms, 
            $payment_method, 
            $payment_details, 
            $totalAmount
        );

        if ($booking_id) {
            file_put_contents('debug.txt', 'Booking ID: ' . $booking_id . PHP_EOL, FILE_APPEND);

            // Clean up session data after booking
            unset($_SESSION['selected_rooms']);
            unset($_SESSION['check_in']);
            unset($_SESSION['check_out']);
            unset($_SESSION['total_amount']);

            header('Location: ' . APP_URL . '/confirmation-done/' . $booking_id);
            exit;
        } else {
            file_put_contents('debug.txt', 'Booking creation failed' . PHP_EOL, FILE_APPEND);
            Flash::set('error', 'Failed to save booking. Please try again.');
            header('Location: ' . APP_URL);
            exit;
        }
    }

    public function confirmationDone($booking_id)
    {
        $booking = $this->bookingModel->getBookingById($booking_id);

        if (!$booking) {
            (new ErrorController())->notFound("Booking not found");
            return;
        }

        $this->view('home/confirmation-done', ['booking' => $booking]);
    }

    public function downloadInvoice($booking_id)
    {
        $booking = $this->bookingModel->getBookingById($booking_id);

        if (!$booking) {
            (new ErrorController())->notFound("Booking not found");
            return;
        }

        // Enable remote loading (for images via URL)
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);

        // Build HTML invoice
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 14px; margin: 20px; }
                .header { text-align: center; }
                .logo { width: 100px; }
                h2 { margin-bottom: 5px; }
                .contact { font-size: 12px; margin-bottom: 20px; }
                .details, .rooms { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .details td { padding: 5px; }
                .rooms th, .rooms td { border: 1px solid #ddd; padding: 8px; }
                .rooms th { background-color: #f2f2f2; }
                .footer { text-align: center; font-size: 12px; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . APP_URL . '/assets/images/logo.png" class="logo" alt="Logo">
                <h2>Cherating Guest House</h2>
                <p class="contact">Contact: +601111034533 | Address: 4/1000 Kampung Budaya, Jalan Kampung Cherating Lama, 26080 Kuantan, Pahang</p>
            </div>

            <h3>Invoice - Booking Ref: ' . htmlspecialchars($booking['booking_ref_no']) . '</h3>

            <table class="details">
                <tr>
                    <td><strong>Name:</strong></td>
                    <td>' . htmlspecialchars($booking['full_name']) . '</td>
                </tr>
                <tr>
                    <td><strong>Check-in:</strong></td>
                    <td>' . htmlspecialchars($booking['check_in']) . '</td>
                </tr>
                <tr>
                    <td><strong>Check-out:</strong></td>
                    <td>' . htmlspecialchars($booking['check_out']) . '</td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td>RM ' . number_format($booking['total_amount'], 2) . '</td>
                </tr>
            </table>

            <h4>Rooms Booked</h4>
            <table class="rooms">
                <thead>
                    <tr>
                        <th>Room Name</th>
                        <th>Quantity</th>
                        <th>Price (RM)</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($booking['rooms'] as $room) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($room['name']) . '</td>
                        <td>' . $room['rooms_booked'] . '</td>
                        <td>' . number_format($room['price'], 2) . '</td>
                    </tr>';
        }
        $html .= '
                </tbody>
            </table>

            <div class="footer">
                Thank you for booking with us!
            </div>
        </body>
        </html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("invoice_{$booking['booking_ref_no']}.pdf", ["Attachment" => true]);
        exit;
    }

    public function handleContactForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $message = trim($_POST['message'] ?? '');

            // Simple validation
            if (!$name || !$email || !$message) {
                $_SESSION['error'] = 'Please fill in all required fields.';
                header('Location: ' . APP_URL . '/contact');
                exit;
            }
        }

        // save to database
        $saved = $this->contactModel->saveContact($name, $email, $phone, $message);

        // WhatsApp API simulation
        if ($saved) {
            $wa_url = WhatsappHelper::generateWhatsAppLink($name, $email, $phone, $message);

            // Save it in session to redirect from front-end
            $_SESSION['whatsapp_url'] = $wa_url;
            $_SESSION['success'] = 'Your message has been sent successfully!';
        } else {
            $_SESSION['error'] = 'Failed to send your message. Please try again.';
        }

        header('Location: ' . APP_URL . '/contact');
        exit;
    }
}