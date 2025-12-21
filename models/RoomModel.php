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
    public function getAvailableRoomsWithCounts($arrival, $departure, $guests = 1)
    {
        // Logic: If guests >= 4, show rooms with capacity 2 or more.
        // If guests < 4, show all active rooms.
        $minCapacity = ($guests >= 4) ? 2 : 1;

        // Fetch all active rooms
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE status = 'active' AND capacity >= :min_cap");
        $stmt->execute([':min_cap' => $minCapacity]);
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

    public function getRoomReviews($room_id)
    {
        $stmt = $this->db->prepare("
            SELECT rr.*, c.full_name 
            FROM room_reviews rr
            JOIN customers c ON rr.customer_id = c.id
            WHERE rr.room_id = ?
            ORDER BY rr.created_at DESC
        ");
        $stmt->execute([$room_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function canUserReview($customer_id, $room_id)
    {
        // A user can review if they have a 'confirmed' booking 
        // for this room_id where the check_out date has passed.
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM bookings b
            JOIN booking_rooms br ON b.id = br.booking_id
            WHERE b.customer_id = ? 
            AND br.room_id = ? 
            AND b.status = 'confirmed' 
            AND b.check_out < CURDATE()
        ");
        $stmt->execute([$customer_id, $room_id]);

        return $stmt->fetchColumn() > 0;
    }

    public function saveReview($room_id, $customer_id, $rating, $comment)
    {
        $stmt = $this->db->prepare("
            INSERT INTO room_reviews (room_id, customer_id, rating, comment, created_at) 
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");

        return $stmt->execute([$room_id, $customer_id, $rating, $comment]);
    }

    public function getAverageRating($room_id)
    {
        $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM room_reviews WHERE room_id = ?");
        $stmt->execute([$room_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch adjustments where the holiday period overlaps with booking dates
    public function getPriceAdjustments($arrival, $departure)
    {
        $sql = "SELECT * FROM price_adjustments 
                WHERE (start_date <= :departure AND end_date >= :arrival)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['arrival' => $arrival, 'departure' => $departure]);
        
        return $stmt->fetchAll();
    }
}