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
        $arrival = $_POST['arrival_date'] ?? $_SESSION['check_in'] ?? null;
        $departure = $_POST['departure_date'] ?? $_SESSION['check_out'] ?? null;
        $selectedRooms = $_POST['rooms'] ?? $_SESSION['selected_rooms'] ?? [];
        $totalAmount = 0;

        if (!$arrival || !$departure || empty($selectedRooms)) {
            Flash::set('error', 'Please select dates and rooms');
            header('Location: ' . APP_URL);
            exit;
        }

        // --- 1. Calculate Total Nights ---
        try {
            $checkDate = new \DateTime($arrival);
            $endDate = new \DateTime($departure);
            // The difference in days is the number of nights
            $interval = $checkDate->diff($endDate);
            $totalNights = $interval->days;

            if ($totalNights <= 0) {
                Flash::set('error', 'Departure date must be after arrival date.');
                header('Location: ' . APP_URL);
                exit;
            }
        } catch (\Exception $e) {
            Flash::set('error', 'Invalid date format.');
            header('Location: ' . APP_URL);
            exit;
        }

        // Check if the selected dates are fully booked
        $fullDates = $this->bookingModel->getFullyBookedDates();

        // Create a temporary clone for the loop so we don't modify the original arrival object
        $loopDate = clone $checkDate;
        
        // Check if the arrival date is in the "Full" list
        while ($loopDate < $endDate) {
            $formattedDate = $loopDate->format('Y-m-d');
            if (in_array($formattedDate, $fullDates)) {
                // die("Date $formattedDate is full!");
                Flash::set('error', "Sorry! The guesthouse is fully booked on " . $loopDate->format('d/m/Y') . ". Please choose different dates.");
                header('Location: ' . APP_URL);
                exit;
            }
            $loopDate->modify('+1 day');
        }

        // 4. Room Processing & Cost Calculation
        $rooms = $this->roomModel->getRoomsByIds(array_keys($selectedRooms));
        $costPerNight = 0.0;
        $rooms_for_session = [];

        foreach ($rooms as $room) {
            $roomId = (int)$room['id'];
            $quantity = (int)($selectedRooms[$roomId] ?? 0);

            if ($quantity > 0) {
                if ($quantity > (int)$room['total_rooms']) {
                    Flash::set('error', "You requested $quantity {$room['name']}(s), but we only have {$room['total_rooms']} units in total.");
                    header('Location: ' . APP_URL);
                    exit;
                }

                $room['quantity'] = $quantity;
                $rooms_for_session[] = $room;

                // Calculate the subtotal for this room type for one night
                $subtotal = (float)$room['price'] * $quantity;
                $costPerNight += $subtotal;
            }
        }

        // 5. Calculate Final Total Amount
        $totalAmount = $costPerNight * $totalNights;

        $merchantId = 'YOUR_MERCHANT_ID';
        $qrUrl = "https://www.tngdigital.com.my/pay?amount=" . number_format($totalAmount, 2, '.', '') . "&merchant_id=" . $merchantId;

        // 6. Save selected rooms and booking details to session
        $_SESSION['selected_rooms'] = $selectedRooms; // e.g. [2 => 1, 5 => 2]
        $_SESSION['rooms_data'] = $rooms_for_session;
        $_SESSION['check_in'] = $arrival;
        $_SESSION['check_out'] = $departure;
        $_SESSION['total_amount'] = $totalAmount;
        $_SESSION['total_nights'] = $totalNights;

        // 7. Render View
        $this->view('home/booking-confirmation', [
            'arrival' => $arrival,
            'departure' => $departure,
            'rooms' => $rooms_for_session,
            'totalAmount' => $totalAmount,
            'totalNights' => $totalNights,
            'qrUrl' => $qrUrl,
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

        // Validate if receipt is uploaded
        if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
            Flash::set('error', 'Please upload your payment receipt.');
            header('Location: ' . APP_URL . '/booking-confirmation');
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
                $this->handleFinalBooking($customerId, $_POST, $_FILES['receipt']);
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
            $this->handleFinalBooking($customerId, $_POST, $_FILES['receipt']);
        }
    }

    private function handleFinalBooking($customerId, $postData, $fileData)
    {
        // 1. Retrieve essential details
        $check_in = $postData['check_in']; 
        $check_out = $postData['check_out'];
        $rooms = $postData['rooms'];
        $payment_method = $postData['payment_method'];
        $totalAmount = $postData['total_amount'] ?? 0.0;
        $payment_details = '';
        $totalNights = $_SESSION['total_nights'] ?? 1;

        $uploadDir = 'uploads/receipts/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileExtension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $fileName = 'receipt_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            Flash::set('error', 'Failed to save the receipt image.');
            header('Location: ' . APP_URL . '/booking-confirmation');
            exit;
        }

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
            $totalAmount,
            $totalNights
        );

        // 4. Handle Result
        if ($booking_id) {
            $this->bookingModel->addPayment([
                'booking_id'      => $booking_id,
                'payment_ref_no'  => 'PAY-' . strtoupper(uniqid()),
                'payment_method'  => $payment_method,
                'amount'          => $totalAmount, // Or set a specific deposit amount
                'payment_type'    => 'deposit',
                'receipt_image'   => $targetPath,
                'status'          => 'pending'
            ]);

            // Clean up session and redirect to confirmation page
            unset($_SESSION['selected_rooms']);
            unset($_SESSION['pending_booking_data']);
            unset($_SESSION['check_in']);
            unset($_SESSION['check_out']);
            unset($_SESSION['total_amount']);
            unset($_SESSION['total_nights']);
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
                    <td>' . $nights . '</td>
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
                    <img src="' . APP_URL . '/assets/images/Cherating_Indah_Logo.png" class="logo" alt="Logo">
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

                <h4>Rooms Booked: (' . $nights . ' Night(s))</h4>
                <table class="rooms">
                    <thead>
                        <tr>
                            <th>Room Name</th>
                            <th>Quantity</th>
                            <th>Price per Night (RM)</th>
                            <th>Total Night(s) Stayed</th>
                            <th>Subtotal (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $html_rooms . '
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;">Total Amount:</td>
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

    /**
     * Handles the display of the booking confirmation page when accessed via GET 
     * (e.g., after a redirect from the confirmBooking POST logic).
    */
    public function handleConfirmationView()
    {
        // Retrieve necessary data from the session
        $arrival = $_SESSION['check_in'] ?? null;
        $departure = $_SESSION['check_out'] ?? null;
        $totalNights = $_SESSION['total_nights'] ?? 0;
        $totalAmount = $_SESSION['total_amount'] ?? 0.0;
        
        // Retrieve room data. We need to fetch room details again, 
        // but use the quantities stored in $_SESSION['selected_rooms'].
        $selectedRooms = $_SESSION['selected_rooms'] ?? [];
        $rooms = [];
        
        if (!empty($selectedRooms)) {
            // Fetch room details from the database
            $rooms = $this->roomModel->getRoomsByIds(array_keys($selectedRooms));

            // Attach the quantity to each room for the view, just like in bookingConfirmation()
            foreach ($rooms as &$room) {
                $roomId = (int)$room['id'];
                $room['quantity'] = (int)($selectedRooms[$roomId] ?? 0);
            }
            unset($room);
        }


        $this->view('home/booking-confirmation', [
            'arrival' => $arrival,
            'departure' => $departure,
            'rooms' => $rooms,
            'totalAmount' => $totalAmount,
            'totalNights' => $totalNights,
        ]);
    }
}