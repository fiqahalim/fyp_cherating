<?php

class BookingModel extends Model
{
    public function createBooking($customerId, $check_in, $check_out, $rooms, $payment_method, $payment_details, $total_amount, $totalNights)
    {
        try {
            // Begin transaction
            $this->db->beginTransaction();

            // Generate a unique booking reference number
            $datePart = date('Ymd');
            $randomPart = strtoupper(substr(uniqid('', true), -6));
            $booking_ref_no = 'CHT-' . $datePart . '-' . $randomPart;

            // Insert main booking record (summary)
            $stmt = $this->db->prepare("
                INSERT INTO bookings 
                (customer_id, booking_ref_no, check_in, check_out, total_nights, total_amount, status)
                VALUES (?, ?, ?, ?, ?, ?, 'confirmed')
            ");

            $stmt->execute([
                $customerId, 
                $booking_ref_no, 
                $check_in, 
                $check_out,
                (int)$totalNights,
                $total_amount
            ]);

            $booking_id = $this->db->lastInsertId();

            // INSERT PAYMENT RECORD (New Block!)
            $payment_ref_no = 'P-' . $booking_ref_no . '-' . date('His');
            $payment_status_default = 'unpaid';

            $stmtPayment = $this->db->prepare("
                INSERT INTO payments 
                (booking_id, payment_ref_no, payment_method, amount, payment_type, remarks, verified)
                VALUES (?, ?, ?, ?, 'full', ?, 'pending')
            ");

            // Assuming $payment_details from the controller is used as a 'remarks' field for now
            $stmtPayment->execute([
                (int)$booking_id,
                $payment_ref_no,
                $payment_method,
                $total_amount,
                $payment_details
            ]);

            // OPTIONAL: Update booking payment status after successful payment record creation
            $stmtUpdate = $this->db->prepare("
                UPDATE bookings 
                SET payment_status = ? 
                WHERE id = ?
            ");
            $stmtUpdate->execute(['unpaid', (int)$booking_id]);

            // Insert each room booked
            $stmtRoom = $this->db->prepare("
                INSERT INTO booking_rooms (booking_id, room_id, rooms_booked)
                VALUES (?, ?, ?)
            ");

            foreach ($rooms as $room_id => $quantity) {
                file_put_contents('debug.txt', "Checking room booking: room_id = " . var_export($room_id, true) . ", quantity = " . var_export($quantity, true) . PHP_EOL, FILE_APPEND);

                // Validate room_id and quantity before insertion
                if (is_numeric($room_id) && is_numeric($quantity) && (int)$room_id > 0 && (int)$quantity > 0) {
                    $stmtRoom->execute([
                        (int)$booking_id,
                        (int)$room_id,
                        (int)$quantity
                    ]);
                    file_put_contents('debug.txt', "✅ Inserted room booking: room_id = $room_id, quantity = $quantity\n", FILE_APPEND);
                } else {
                    file_put_contents('debug.txt', "❌ Invalid room data. Skipping entry.\n", FILE_APPEND);
                }
            }

            $this->db->commit();

            file_put_contents('debug.txt', "Booking created with ID: $booking_id\n", FILE_APPEND);

            return $booking_id;

        } catch (Exception $e) {
            $this->db->rollBack();
            file_put_contents('debug.txt', "BookingModel error: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    public function getBookingById($booking_id)
    {
        // Get booking details with rooms
        $stmt = $this->db->prepare("
            SELECT 
                b.*, 
                c.full_name, 
                c.email, 
                c.phone,
                p.payment_method,
                p.payment_ref_no,
                p.payment_date
            FROM bookings b
            LEFT JOIN customers c ON b.customer_id = c.id
            LEFT JOIN payments p ON b.id = p.booking_id
            WHERE b.id = ?
            ORDER BY p.payment_date DESC
            LIMIT 1
        ");

        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            return null;
        }

        // 2. Fallback logic for payment method/status (optional, but good practice)
        $booking['payment_method'] = $booking['payment_method'] ?? 'N/A';
        $booking['payment_status'] = $booking['payment_status'] ?? 'unpaid';

        // 3. Get associated room details
        $stmtRooms = $this->db->prepare("
            SELECT r.name, r.price, br.rooms_booked 
            FROM booking_rooms br 
            JOIN rooms r ON br.room_id = r.id 
            WHERE br.booking_id = ?
        ");
        $stmtRooms->execute([$booking_id]);
        $booking['rooms'] = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);

        return $booking;
    }

    // Helper function to generate the payment details string based on payment method
    public function generatePaymentDetails($payment_method, $amount, $order_id = null)
    {
        $payment_info = '';

        if ($payment_method === 'card') {
            $payment_info = "Payment Method: Credit Card, Amount:RM" . number_format($amount, 2);
        } elseif ($payment_method === 'paypal') {
            $payment_info = "Payment Method: PayPal, Amount: RM" . number_format($amount, 2);
        } elseif ($payment_method === 'qr') {
            // Generate QR payment URL dynamically (this is an example URL)
            $merchantId = 'YOUR_MERCHANT_ID'; //Replace with actual merchant id
            $payment_info = "Payment Method: QR Code, Amount: RM" . number_format($amount, 2) . ", QR Payment URL: https://www.tngdigital.com.my/pay?amount=" . $amount . "&merchant_id=" . $merchantId . "&order_id=" . $order_id;
        }

        return $payment_info;
    }

    public function getAllBookings($offset = 0, $limit = 10, $search = '')
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

    public function getTotalBookings($search = '')
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

    // Delete booking and related data in dependent tables
    public function deleteBooking($id)
    {
        // Start a transaction to ensure both deletes are handled safely
        $this->db->beginTransaction();

        try {
            // delete related records from booking_rooms
            $stmt = $this->db->prepare("DELETE FROM booking_rooms WHERE booking_id = ?");
            $stmt->execute([$id]);

            // delete the booking itself
            $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
            $stmt->execute([$id]);

            // Commit the transaction
            $this->db->commit();

            return true;
        } catch (Exception $error) {
            // If any error occurs, rollback the transaction
            $this->db->rollBack();
            throw $error;
        }
    }

    public function getBookingsByCustomer($customerId)
    {
        // Get all bookings for the customer
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE customer_id = ? ORDER BY check_in DESC");
        $stmt->execute([$customerId]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add room details for each booking
        foreach ($bookings as &$booking) {
            $stmtRooms = $this->db->prepare("
                SELECT r.name, r.price, br.rooms_booked 
                FROM booking_rooms br 
                JOIN rooms r ON br.room_id = r.id 
                WHERE br.booking_id = ?
            ");
            $stmtRooms->execute([$booking['id']]);
            $booking['rooms'] = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);
        }

        return $bookings;
    }

    // Get fully booked dates:
    public function getFullyBookedDates($daysAhead = 60)
    {
        // 1. Get total number of rooms available in the guesthouse
        $stmtTotal = $this->db->prepare("SELECT SUM(total_rooms) as total_capacity FROM rooms WHERE status = 'active'");
        $stmtTotal->execute();
        $totalCapacity = (int)$stmtTotal->fetchColumn();

        // 2. Query to get total rooms booked per day
        $stmt = $this->db->prepare("
            SELECT b.check_in, b.check_out, br.rooms_booked 
            FROM bookings b
            JOIN booking_rooms br ON b.id = br.booking_id
            WHERE b.status = 'confirmed' 
            AND b.check_out >= CURDATE()
        ");
        $stmt->execute();
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $occupancyMap = [];

        // 3. Process bookings to map occupancy per date
        foreach ($bookings as $b) {
            $start = new DateTime($b['check_in']);
            $end = new DateTime($b['check_out']);
            
            // Interval is 1 day. DatePeriod excludes the 'check_out' day 
            // which is correct (room is free on morning of check-out)
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end);

            foreach ($period as $date) {
                $d = $date->format('Y-m-d');
                if (!isset($occupancyMap[$d])) {
                    $occupancyMap[$d] = 0;
                }
                $occupancyMap[$d] += (int)$b['rooms_booked'];
            }
        }

        // 4. Identify dates where occupancy >= total capacity
        $disabledDates = [];
        foreach ($occupancyMap as $date => $currentOccupancy) {
            if ($currentOccupancy >= $totalCapacity) {
                $disabledDates[] = $date;
            }
        }

        return $disabledDates;
    }

    public function getTotalRevenue()
    {
        // Sum of total_amount from all bookings
        $stmt = $this->db->prepare("SELECT SUM(total_amount) FROM bookings");
        $stmt->execute();
        
        return $stmt->fetchColumn() ?: 0;
    }

    public function getMonthlyRevenue($year = null)
    {
        $year = $year ?: date('Y');
        // Using payment_status = 'paid' ensures we only chart actual income
        $sql = "SELECT MONTH(created_at) as month, SUM(total_amount) as total 
                FROM bookings 
                WHERE YEAR(created_at) = :year AND payment_status = 'paid'
                GROUP BY MONTH(created_at)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnpaidBookings($limit = 5)
    {
        $sql = "SELECT b.*, c.full_name, c.phone 
                FROM bookings b
                JOIN customers c ON b.customer_id = c.id
                WHERE b.payment_status != 'paid' AND b.status != 'cancelled'
                ORDER BY b.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all unique years from the bookings table
    public function getBookingYears()
    {
        $stmt = $this->db->prepare("SELECT DISTINCT YEAR(created_at) as year FROM bookings ORDER BY year DESC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Fetching 'pending' bookings as "New Alerts"
    public function getNewBookings($limit = 4)
    {
        $sql = "SELECT b.*, c.full_name 
                FROM bookings b 
                JOIN customers c ON b.customer_id = c.id 
                WHERE b.status = 'pending' 
                ORDER BY b.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewBookingsCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function addPayment($data)
    {
        $sql = "INSERT INTO payments (booking_id, payment_ref_no, payment_method, amount, payment_type, receipt_image, verified) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['booking_id'],
            $data['payment_ref_no'],
            $data['payment_method'],
            $data['amount'],
            $data['payment_type'],
            $data['receipt_image'],
            $data['status']
        ]);
    }
}