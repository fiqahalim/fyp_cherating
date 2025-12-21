<?php

class RoomVirtualModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTourByRoomId($roomId)
    {
        $sql = "SELECT rv.id as tour_id, r.name, r.description, rv.image_path, rv.title 
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
}