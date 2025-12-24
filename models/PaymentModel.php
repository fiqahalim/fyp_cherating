<?php

class PaymentModel extends Model
{
    public function getAllPayments($offset = 0, $limit = 10, $search = '')
    {
        $searchQuery = "";
        if (!empty($search)) {
            // Search by Ref No, Full Name, or Email
            $searchQuery = " WHERE b.booking_ref_no LIKE :search 
                            OR c.full_name LIKE :search 
                            OR c.email LIKE :search ";
        }

        // Join with customers table to get name, email, and phone
        $sql = "SELECT b.*, c.full_name, c.email, c.phone 
                FROM bookings b
                LEFT JOIN customers c ON b.customer_id = c.id
                $searchQuery
                ORDER BY b.created_at DESC
                LIMIT :offset, :limit";

        $stmt = $this->db->prepare($sql);

        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bindValue(':search', $searchTerm);
        }

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPayments($search = '')
    {
        $searchQuery = "";
        if (!empty($search)) {
            $searchQuery = " LEFT JOIN customers c ON b.customer_id = c.id 
                            WHERE b.booking_ref_no LIKE :search 
                            OR c.full_name LIKE :search 
                            OR c.email LIKE :search ";
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings b $searchQuery");

        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bindValue(':search', $searchTerm);
        }

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function deletePayment($id)
    {
        $this->db->beginTransaction();

        try {

            $stmt = $this->db->prepare("DELETE FROM payments WHERE id = ?");
            $stmt->execute([$id]);

            $this->db->commit();

            return true;
        } catch (Exception $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function addPaymentReceipt($bookingId, $imagePath, $amount)
    {
        $sql = "INSERT INTO payments (booking_id, payment_ref_no, amount, receipt_image, payment_type, verified) 
                VALUES (?, ?, ?, ?, 'full', 'pending')";
        
        $paymentRef = "PAY-" . strtoupper(uniqid());
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$bookingId, $paymentRef, $amount, $imagePath]);
    }

    public function updateVerificationStatus($paymentId, $status, $reason = '')
    {
        $sql = "UPDATE payments SET verified = ?, rejection_reason = ? WHERE id = ?";

        return $this->db->prepare($sql)->execute([$status, $reason, $paymentId]);
    }

    public function getPaymentById($id)
    {
        $sql = "SELECT * FROM payments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
