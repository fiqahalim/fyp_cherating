<?php

class PaymentController extends Controller
{
    private $paymentModel;

    // Constructor to initialize the model
    public function __construct()
    {
        $this->paymentModel = $this->model('PaymentModel');
        $this->bookingModel = $this->model('BookingModel');
    }

    public function index()
    {
        $resultsPerPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $offset = ($currentPage - 1) * $resultsPerPage;

        $totalPayments = $this->paymentModel->getTotalPayments($search);
        $totalPages = ceil($totalPayments / $resultsPerPage);

        $payments = $this->paymentModel->getAllPayments($offset, $resultsPerPage, $search);

        // Pass the pagination data to the view
        $this->view('admin/payments/index', [
            'payments' => $payments,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'offset' => $offset,
            'resultsPerPage' => $resultsPerPage,
            'totalPayments' => $totalPayments,
            'search' => $search
        ]);
    }

    public function updateStatus() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_id = $_POST['payment_id'];
            $status = $_POST['status'];
            $reason = $_POST['reason'] ?? '';
            
            // Update the payment record
            $success = $this->paymentModel->updateVerificationStatus($payment_id, $status, $reason);
            
            if ($success) {
                $payment = $this->paymentModel->getPaymentById($payment_id);

                $booking_id = $payment['booking_id'];
                
                if ($status === 'approved') {
                    $this->bookingModel->updateBookingStatus($booking_id, 'paid', 'confirmed');
                    $_SESSION['success'] = "Payment approved and booking confirmed!";
                } else {
                    $this->bookingModel->updateBookingStatus($booking_id, 'unpaid', 'pending');
                    $_SESSION['warning'] = "Payment rejected. Customer will see the reason.";
                }
                
                $_SESSION['success'] = "Payment has been " . $status;
            }
            
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }
    }

    public function verifyPayment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status']; // 'verified' or 'rejected'
            $reason = $_POST['rejection_reason'] ?? '';

            $success = $this->paymentModel->updateVerificationStatus($id, $status, $reason);

            if ($success) {
                if ($status === 'verified') {
                    $payment = $this->paymentModel->getPaymentById($id);
                    
                    $this->bookingModel->updateBookingStatus(
                        $payment['booking_id'], 
                        'paid',      // payment_status
                        'confirmed'  // booking_status
                    );
                }
                Flash::set('success', 'Payment status updated to ' . strtoupper($status));
            } else {
                Flash::set('error', 'Failed to update status.');
            }
            
            header('Location: ' . APP_URL . '/admin/payments/verify/' . $id);
            exit;
        }

        $payment = $this->paymentModel->getPaymentById($id);

        if (!$payment) {
            Flash::set('error', 'No payment record found.');
            header("Location: " . APP_URL . "/admin/payments");
            exit;
        }
        
        $this->view('admin/payments/view', ['payment' => $payment]);
    }
}