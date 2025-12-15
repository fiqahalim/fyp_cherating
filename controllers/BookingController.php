<?php

class BookingController extends Controller
{
    // Declare the model property
    private $bookingModel;

    // Constructor to initialize the model
    public function __construct()
    {
        // Initialize the model (only once)
        $this->bookingModel = $this->model('BookingModel');
    }

    public function index()
    {
        // Number of results per page
        $resultsPerPage = 10;

        // Get the current page number from the URL, default to 1
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Calculate the offset for SQL query
        $offset = ($currentPage - 1) * $resultsPerPage;

        // Get the total number of bookings
        $totalBookings = $this->bookingModel->getTotalBookings();
        $totalPages = ceil($totalBookings / $resultsPerPage);

        // Fetch the bookings for the current page
        $bookings = $this->bookingModel->getAllBookings($offset, $resultsPerPage);

        // Pass the pagination data to the view
        $this->view('admin/bookings/index', [
            'bookings' => $bookings,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }

    // view bookings detail
    public function viewBooking($id)
    {
        // get the booking details by ID
        $booking = $this->bookingModel->getBookingById($id);

        if (!$booking) {
            // If no booking found, redirect with an error message
            Flash::set('error', 'Booking not found');
            header('Location: ' . APP_URL . '/admin/bookings');
            exit;
        }

        // Pass the booking data to the view
        $this->view('admin/bookings/view', ['booking' => $booking]);
    }

    // Delete bookings
    public function delete($id)
    {
        // check if the booking exists
        $booking = $this->bookingModel->getBookingById($id);

        if (!$booking) {
            Flash::set('error', 'Booking not found');
            header('Location: ' . APP_URL . '/admin/bookings');
            exit;
        }

        $this->bookingModel->deleteBooking($id);

        Flash::set('success', 'Booking deleted successfully');
        header('Location: ' . APP_URL . '/admin/bookings');
        exit;
    }

    /**
     * Displays the customer's personal booking dashboard, 
     * categorized into Upcoming and Past bookings.
    */
    public function customerDashboard()
    {
        // 1. Authentication Check & Customer ID Retrieval
        // Assuming you use sessions for login and store the customer ID.
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
            // Redirect if not logged in or not a customer
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $customerId = (int)$_SESSION['user_id'];
        
        // 2. Fetch all bookings for the customer
        $bookings = $this->bookingModel->getBookingsByCustomer($customerId);

        // 3. Sort Bookings into Upcoming and Past
        $currentDate = date('Y-m-d');
        $upcomingBookings = [];
        $pastBookings = [];

        if (!empty($bookings)) {
            foreach ($bookings as $booking) {
                // Check-out date is used to determine if the trip is still ongoing or upcoming.
                if ($booking['check_out'] >= $currentDate) {
                    $upcomingBookings[] = $booking;
                } else {
                    $pastBookings[] = $booking;
                }
            }
        }
        
        // 4. Load the enhanced dashboard view
        $data = [
            'upcomingBookings' => $upcomingBookings,
            'pastBookings' => $pastBookings,
        ];

        $this->view('home/dashboard', $data); 
    }
}