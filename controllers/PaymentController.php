<?php

class PaymentController extends Controller
{
    private $paymentModel;

    // Constructor to initialize the model
    public function __construct()
    {
        $this->paymentModel = $this->model('PaymentModel');
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
}