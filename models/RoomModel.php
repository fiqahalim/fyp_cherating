<?php

class RoomModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Create a new room
    public function createRoom($name, $description, $price, $total_rooms, $capacity, $image, $status)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO rooms (name, description, price, total_rooms, capacity, image, status)
             VALUES (:name, :description, :price, :total_rooms, :capacity, :image, :status)"
        );

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':total_rooms', $total_rooms);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Get all active rooms
    public function getAllRooms()
    {
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE status = 'active'");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single room by ID
    public function getRoomById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get rooms by IDs (for confirmation)
    public function getRoomsByIds(array $ids)
    {
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE id IN ($in)");
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get number of available rooms for a room type during a date range
    public function getAvailableRooms($room_id, $arrival, $departure)
    {
        // Get total rooms for this type
        $stmt = $this->db->prepare("SELECT total_rooms FROM rooms WHERE id = ?");
        $stmt->execute([$room_id]);
        $totalRooms = (int)$stmt->fetchColumn();

        // Count overlapping confirmed bookings for this room type
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM bookings 
            WHERE room_id = ? 
            AND status = 'confirmed'
            AND NOT (check_out <= ? OR check_in >= ?)
        ");
        $stmt->execute([$room_id, $arrival, $departure]);
        $booked = (int)$stmt->fetchColumn();

        return max(0, $totalRooms - $booked);
    }

    // Get rooms with their availability counts
    public function getAvailableRoomsWithCounts($arrival, $departure)
    {
        // Fetch all active rooms
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE status = 'active'");
        $stmt->execute();
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // For each room, calculate available rooms
        foreach ($rooms as &$room) {
            // Fetch the total number of rooms booked during the given dates
            $stmt = $this->db->prepare(
                "SELECT SUM(br.rooms_booked) AS booked_quantity 
                FROM booking_rooms br
                JOIN bookings b ON br.booking_id = b.id
                WHERE br.room_id = :room_id
                AND b.status != 'cancelled' 
                AND ((b.check_in <= :departure AND b.check_out >= :arrival) 
                OR (b.check_in <= :arrival AND b.check_out >= :arrival))"
            );
            $stmt->execute([
                ':room_id' => $room['id'],
                ':arrival' => $arrival,
                ':departure' => $departure
            ]);

            // Fetch booked quantity
            $booked = $stmt->fetch(PDO::FETCH_ASSOC)['booked_quantity'] ?? 0;

            // Calculate available rooms (total rooms - booked rooms)
            $room['available'] = max(0, $room['total_rooms'] - $booked);
        }

        return $rooms;
    }

    // Update room
    public function updateRoom($id, $name, $description, $price, $total_rooms, $capacity, $image, $status)
    {
        $stmt = $this->db->prepare(
            "UPDATE rooms SET name = :name, description = :description, price = :price,
             total_rooms = :total_rooms, capacity = :capacity, image = :image, status = :status
             WHERE id = :id"
        );

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':total_rooms', $total_rooms);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Delete room
    public function deleteRoom($id)
    {
        $stmt = $this->db->prepare("DELETE FROM rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalRooms()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM rooms");
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}