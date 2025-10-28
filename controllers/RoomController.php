<?php

class RoomController extends Controller
{
    // Declare the model property
    private $roomModel;

    // Constructor to initialize the model
    public function __construct()
    {
        // Initialize the model
        $this->roomModel = $this->model('RoomModel');
    }

    // List all rooms
    public function index()
    {
        // Number of results per page
        $resultsPerPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Calculate the offset for SQL query
        $offset = ($currentPage - 1) * $resultsPerPage;

        // Get the total number of bookings
        $totalRooms = $this->roomModel->getTotalRooms();
        $totalPages = ceil($totalRooms / $resultsPerPage);

        $rooms = $this->roomModel->getAllRooms($offset, $resultsPerPage);

        // Pass the pagination data to the view
        $this->view('admin/rooms/index', [
            'rooms' => $rooms,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }

    // Create or update a room
    public function createOrUpdate($id = null)
    {
        $room = null;

        // If ID is given, fetch existing room for edit
        if ($id) {
            $room = $this->roomModel->getRoomById($id);
            if (!$room) {
                Flash::set('error', 'Room not found');
                header('Location: ' . APP_URL . '/admin/rooms');
                exit;
            }
        }

        // If form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $total_rooms = $_POST['total_rooms'];
            $capacity = $_POST['capacity'];
            $status = $_POST['status'];

            $imagePath = $room['image'] ?? null;

            // Handle image upload if provided
            if (!empty($_FILES['image']['name'])) {
                $image = $_FILES['image']['name'];
                $targetDir = __DIR__ . '/../public/uploads/';
                $imagePath = '/uploads/' . basename($image);
                move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . basename($image));
            }

            if ($id) {
                $this->roomModel->updateRoom($id, $name, $description, $price, $total_rooms, $capacity, $imagePath, $status);
                Flash::set('success', 'Room updated successfully');
            } else {
                $this->roomModel->createRoom($name, $description, $price, $total_rooms, $capacity, $imagePath, $status);
                Flash::set('success', 'Room created successfully');
            }

            header('Location: ' . APP_URL . '/admin/rooms');
            exit;
        }

        // Load form view
        $this->view('admin/rooms/create-edit', ['room' => $room]);
    }

    // Delete a room
    public function delete($id)
    {
        $this->roomModel->deleteRoom($id);

        Flash::set('success', 'Room deleted successfully');
        header('Location: ' . APP_URL . '/admin/rooms');
        exit;
    }
}