<?php

class RoomVirtualController extends Controller
{
    // Declare the model property
    private $roomModel, $roomVirtualModel;

    // Constructor to initialize the model
    public function __construct()
    {
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

        $this->view('admin/room-virtuals/index', [
            'roomVirtuals' => $roomVirtuals,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }

    public function viewRoomVirtual($id)
    {
        $roomVirtual = $this->roomVirtualModel->getTourByRoomId($id);

        if (!$roomVirtual) {
            Flash::set('error', '360° Virtual Tour not found');
            header('Location: ' . APP_URL . '/admin/room-virtuals');
            exit;
        }

        $hotspots = $this->roomVirtualModel->getHotspotsByTourId($roomVirtual['tour_id']);

        $this->view('admin/room-virtuals/view', [
            'roomVirtual' => $roomVirtual,
            'hotspots'    => $hotspots,
        ]);
    }

    public function createOrUpdate($id = null)
    {
        $roomVirtual = null;
        $hotspots = [];

        if ($id) {
            $roomVirtual = $this->roomVirtualModel->getTourByRoomId($id);
            if ($roomVirtual) {
                $hotspots = $this->roomVirtualModel->getHotspotsByTourId($roomVirtual['tour_id']);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_id' => $_POST['room_id'],
                'title'   => $_POST['title'],
                'image_path' => $roomVirtual ? $roomVirtual['image_path'] : '' 
            ];

            if (isset($_FILES['panorama_image']) && $_FILES['panorama_image']['error'] === 0) {
                $uploadDir = 'uploads/virtual_tours/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $fileName = time() . '_' . $_FILES['panorama_image']['name'];
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['panorama_image']['tmp_name'], $targetPath)) {
                    $data['image_path'] = $targetPath;
                }
            }

            if ($id) {
                $this->roomVirtualModel->updateTour($roomVirtual['tour_id'], $data);
                $tourId = $roomVirtual['tour_id'];
            } else {
                $tourId = $this->roomVirtualModel->createTour($data);
            }

            $this->roomVirtualModel->deleteHotspots($tourId);
            if (!empty($_POST['hotspots'])) {
                foreach ($_POST['hotspots'] as $spot) {
                    if (!empty($spot['text'])) {
                        $this->roomVirtualModel->addHotspot($tourId, $spot);
                    }
                }
            }

            Flash::set('success', 'Virtual tour saved successfully!');
            header('Location: ' . APP_URL . '/admin/room-virtuals');
            exit;
        }

        $rooms = $this->roomModel->getAllRooms();
        
        $this->view('admin/room-virtuals/create-edit', [
            'roomVirtual' => $roomVirtual,
            'hotspots'    => $hotspots,
            'rooms'       => $rooms,
            'isEdit'      => $id ? true : false
        ]);
    }
}