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

        // 1. Retrieve Data
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

        // 2. Validate inputs
        if (!$name || !$phone || !$email || !$check_in || !$check_out || empty($rooms)) {
            Flash::set('error', 'Please fill in all required fields.');
            header('Location: ' . APP_URL);
            exit;
        }

        // 3. Customer Existence Check & Flow Control
        $customerExists = $this->customerModel->checkExistence($email);
        $customerId = null;

        if (!$customerExists) {
            if (!empty($username) && !empty($password)) {
                $customerId = $this->customerModel->findOrCreateGuest($name, $phone, $email, $username, $password);
                
                if (!$customerId) {
                    Flash::set('error', 'Account creation failed. The username or email might already be registered. Please try a different username.');

                    $_SESSION['show_new_customer_fields'] = true;
                    header('Location: ' . APP_URL . '/booking-confirmation');
                    exit;
                }
                // Customer created successfully, proceed to booking finalization
                $this->handleFinalBooking($customerId, $_POST);
            } else {
                $_SESSION['pending_booking_data'] = $_POST;

                Flash::set('prompt', 'It looks like you are a new customer. Please provide a **username** and **password** to create your account and complete the booking.', 'warning');

                $_SESSION['show_new_customer_fields'] = true;

                header('Location: ' . APP_URL . '/booking-confirmation'); 
                exit;
            }
        } else {
            $customerData = $this->customerModel->getByEmail($email);
            $customerId = $customerData['id'] ?? null;

            if (!$customerId) {
                Flash::set('error', 'Could not retrieve customer account data. Please check your email.');
                header('Location: ' . APP_URL . '/booking-confirmation');
                exit;
            }

            // Customer exists and is identified, proceed to final booking
            $this->handleFinalBooking($customerId, $_POST);
        }
    }

    private function handleFinalBooking($customerId, $postData)
    {
        // 1. Retrieve essential details
        $check_in = $postData['check_in']; 
        $check_out = $postData['check_out'];
        $rooms = $postData['rooms'];
        $payment_method = $postData['payment_method'];
        $totalAmount = $postData['total_amount'] ?? 0.0;
        $payment_details = '';

        // 2. Recalculate payment details
        if ($payment_method === 'card') {
            $payment_details = 'Card ending in ' . substr($postData['card_number'] ?? '', -4);
        } elseif ($payment_method === 'paypal') {
            $payment_details = 'PayPal Email: ' . ($postData['paypal_email'] ?? 'N/A');
        } elseif ($payment_method === 'qr') {
            $orderId = uniqid();
            $payment_details = "QR Payment - Amount: RM" . number_format($totalAmount, 2) . ", Order ID: " . $orderId;
        }

        // 3. Save Booking Record
        $booking_id = $this->bookingModel->createBooking(
            $customerId, 
            $check_in,
            $check_out,
            $rooms, 
            $payment_method, 
            $payment_details, 
            $totalAmount
        );

        // 4. Handle Result
        if ($booking_id) {
            // Clean up session and redirect to confirmation page
            unset($_SESSION['selected_rooms']);
            unset($_SESSION['pending_booking_data']);
            unset($_SESSION['check_in']);
            unset($_SESSION['check_out']);
            unset($_SESSION['total_amount']);
            unset($_SESSION['show_new_customer_fields']);

            header('Location: ' . APP_URL . '/confirmation-done/' . $booking_id);
            exit;
        } else {
            file_put_contents('debug.txt', 'Booking creation failed' . PHP_EOL, FILE_APPEND);
            Flash::set('error', 'Failed to save booking. Please try again.');
            header('Location: ' . APP_URL . '/booking-confirmation');
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

        $nights = max(1, (strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24));
        
        $calculatedTotal = 0.0;
        $html_rooms = '';
        
        // 1. Iterate through rooms to build HTML rows and calculate the true total
        foreach ($booking['rooms'] as $room) {
            // Calculate subtotal for the room (Price per night * Quantity * Number of nights)
            $subtotal = $room['price'] * $room['rooms_booked'] * $nights;
            $calculatedTotal += $subtotal;

            $html_rooms .= '
                <tr>
                    <td>' . htmlspecialchars($room['name']) . '</td>
                    <td>' . $room['rooms_booked'] . '</td>
                    <td>' . number_format($room['price'], 2) . '</td>
                    <td>' . number_format($subtotal, 2) . '</td>
                </tr>';
        }
        
        // 2. Build the final HTML using the calculated total
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Invoice - ' . htmlspecialchars($booking['booking_ref_no']) . '</title>
            <style>
                /* Styles specific to printing (hides buttons/unnecessary elements) */
                @media print {
                    .no-print {
                        display: none !important;
                    }
                }
                body { font-family: Arial, sans-serif; font-size: 14px; margin: 20px; }
                .container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
                .header { text-align: center; margin-bottom: 20px; }
                .logo { max-width: 100px; height: auto; }
                h2 { margin-bottom: 5px; }
                .contact { font-size: 12px; margin-bottom: 20px; }
                .details, .rooms { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .details td { padding: 5px; }
                .rooms th, .rooms td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .rooms th { background-color: #f2f2f2; }
                .total-row td { font-weight: bold; background-color: #f2f2f2; }
                .footer { text-align: center; font-size: 12px; margin-top: 30px; }
            </style>
        </head>
        <body onload="window.print();">
            <div class="container">
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
                        <td><strong>Email:</strong></td>
                        <td>' . htmlspecialchars($booking['email']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Payment Method:</strong></td>
                        <td>' . htmlspecialchars(ucfirst($booking['payment_method'] ?? 'N/A')) . '</td>
                    </tr>
                    <!-- Removed redundant and incorrect Total Amount display from header table -->
                </table>

                <h4>Rooms Booked (' . $nights . ' Night(s))</h4>
                <table class="rooms">
                    <thead>
                        <tr>
                            <th>Room Name</th>
                            <th>Quantity</th>
                            <th>Price per Night (RM)</th>
                            <th>Subtotal (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $html_rooms . '
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;">Total Amount:</td>
                            <td>RM ' . number_format($calculatedTotal, 2) . '</td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer">
                    Thank you for booking with us!
                </div>
            </div>
        </body>
        </html>';

        echo $html;
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