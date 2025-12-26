<?php

class RoomVirtualController extends Controller
{
    // Declare the model property
    private $roomModel;

    // Constructor to initialize the model
    public function __construct()
    {
        // Initialize the model
        $this->roomModel = $this->model('RoomModel');
        $this->roomVirtualModel = $this->model('RoomVirtualModel');
    }

    public function index()
    {
        $resultsPerPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $offset = ($currentPage - 1) * $resultsPerPage;

        $totalRoomVirtuals = $this->roomModel->getTotalRooms();
        $totalPages = ceil($totalRoomVirtuals / $resultsPerPage);

        $roomVirtuals = $this->roomVirtualModel->getAllRoomVirtuals($offset, $resultsPerPage);

        // Pass the pagination data to the view
        $this->view('admin/room-virtuals/index', [
            'roomVirtuals' => $roomVirtuals,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }
}