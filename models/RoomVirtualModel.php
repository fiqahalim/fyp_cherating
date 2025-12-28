<?php

class RoomVirtualModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllRoomVirtuals()
    {
        $stmt = $this->db->prepare("SELECT * FROM room_virtual_tours");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTourByRoomId($roomId)
    {
        $sql = "SELECT rv.id as tour_id, r.id as room_id, r.name, r.description, rv.image_path, rv.title 
                FROM rooms r 
                JOIN room_virtual_tours rv ON r.id = rv.room_id 
                WHERE r.id = ? LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roomId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getHotspotsByTourId($tourId)
    {
        $sql = "SELECT pitch, yaw, text FROM room_tour_hotspots WHERE tour_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tourId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTour($data)
    {
        $stmt = $this->db->prepare("INSERT INTO room_virtual_tours (room_id, title, image_path) VALUES (?, ?, ?)");
        $stmt->execute([$data['room_id'], $data['title'], $data['image_path']]);

        return $this->db->lastInsertId();
    }

    public function updateTour($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE room_virtual_tours SET room_id = ?, title = ?, image_path = ? WHERE id = ?");

        return $stmt->execute([$data['room_id'], $data['title'], $data['image_path'], $id]);
    }

    public function deleteHotspots($tourId)
    {
        $stmt = $this->db->prepare("DELETE FROM room_tour_hotspots WHERE tour_id = ?");

        return $stmt->execute([$tourId]);
    }

    public function addHotspot($tourId, $spot)
    {
        $stmt = $this->db->prepare("INSERT INTO room_tour_hotspots (tour_id, pitch, yaw, text) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$tourId, $spot['pitch'], $spot['yaw'], $spot['text']]);
    }
}