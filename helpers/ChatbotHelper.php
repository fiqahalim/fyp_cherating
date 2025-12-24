<?php

class ChatbotHelper
{
    private static $testMode = true; // Set to FALSE when you have OpenAI credits

    public static function getResponse($userMessage) {
        if (self::$testMode) {
            return self::getMockResponse($userMessage);
        }

        $apiKey = OPENAI_KEY;
        $url = 'https://api.openai.com/v1/chat/completions';

        // This is where you define your "Scope"
        $hotelData = "
            You are an assistant for Cherating Guest House. 
            Facilities: Swimming pool, Free WiFi, Cafe, BBQ area.
            Check-in: 3:00 PM. Check-out: 12:00 PM.
            Room Types: 
            - Standard (RM150/night)
            - Deluxe (RM250/night)
            - Family (RM400/night)
            Booking steps: 1. Select rooms, 2. Enter details, 3. Pay deposit via FPX/QR.
        ";

        $data = [
            'model' => 'gpt-3.5-turbo', // Or gpt-4o-mini (cheaper/faster)
            'messages' => [
                ['role' => 'system', 'content' => $hotelData],
                ['role' => 'user', 'content' => $userMessage]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? "Sorry, I'm having trouble connecting.";
    }

    private static function getMockResponse($userMessage)
    {
        $userMessage = strtolower($userMessage);

        // 1. Handle ROOM PRICES (Keyword: price, room)
        if (strpos($userMessage, 'price') !== false || strpos($userMessage, 'room') !== false) {
            try {
                $dbInstance = Database::getInstance();
                $pdo = $dbInstance->getConnection();

                // Attempt to fetch real room data
                $stmt = $pdo->query("SELECT name, price FROM rooms WHERE status = 'active'");
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($rooms) {
                    $reply = "<b>Current Room Rates:</b><br><br>";
                    $reply .= "<table style='width:100%; border-collapse: collapse; font-size: 12px; background: white; color: black; border: 1px solid #ccc;'>";
                    $reply .= "<tr style='background-color: #007bff; color: white;'>
                                <th style='padding: 6px; border: 1px solid #ccc; text-align: left;'>Room Type</th>
                                <th style='padding: 6px; border: 1px solid #ccc; text-align: center;'>Price</th>
                              </tr>";
                    
                    foreach ($rooms as $room) {
                        $reply .= "<tr>";
                        $reply .= "<td style='padding: 6px; border: 1px solid #ccc;'>" . htmlspecialchars($room['name']) . "</td>";
                        $reply .= "<td style='padding: 6px; border: 1px solid #ccc; text-align: center;'>RM" . number_format($room['price'], 2) . "</td>";
                        $reply .= "</tr>";
                    }
                    $reply .= "</table>";
                    $reply .= "<br><a href='/fyp_cherating/rooms' style='display:inline-block; padding:5px 10px; background:#28a745; color:white; border-radius:5px; text-decoration:none; font-size:12px;'>Book Now</a>";
                    return $reply;
                } else {
                    return "We are currently updating our room list. Please check our Rooms page soon!";
                }
            } catch (Throwable $e) {
                return "Our room prices range from RM50.00 to RM500.00. Please view the full list on our <a href='/fyp_cherating/rooms'>Rooms Page</a>.";
            }
        }

        // 2. Handle CHECK-IN/OUT (Keyword: check, time, hour)
        if (strpos($userMessage, 'check') !== false || strpos($userMessage, 'time') !== false || strpos($userMessage, 'hour') !== false) {
            return "Our <b>Check-in</b> time is 3:00 PM and <b>Check-out</b> time is 12:00 PM. Early check-in is subject to availability.";
        }

        // 3. Handle FACILITIES (Keyword: facility, pool, wifi, bbq)
        if (preg_match('/(facility|facilities|pool|wifi|bbq)/', $userMessage)) {
            return "At Cherating Guest House, we provide:<br>• 🏊 Swimming Pool<br>• 📶 Free High-speed WiFi<br>• ☕ Cafe & BBQ Area<br>• 🚗 Free Parking for guests";
        }

        // 4. Handle BOOKING STEPS (Keyword: book, step, how)
        if (preg_match('/(book|step|how)/', $userMessage)) {
            return "<b>Booking Process:</b><br>
                    1. Select dates & guest count on the Home page.<br>
                    2. Choose your preferred room.<br>
                    3. Enter your details and account info.<br>
                    4. Pay a 35% deposit via <b>FPX</b> or <b>QR Payment</b>.<br>
                    5. Your booking is confirmed instantly!";
        }

        // 5. DEFAULT FALLBACK (If no keywords match)
        return "I'm not quite sure about that. Try asking me about:<br><br>
                <a href='#' onclick=\"sendQuickMsg('room prices')\" style='color: #007bff; text-decoration: underline;'>💰 Room Prices</a><br>
                <a href='#' onclick=\"sendQuickMsg('check-in time')\" style='color: #007bff; text-decoration: underline;'>⏰ Check-in Time</a><br>
                <a href='#' onclick=\"sendQuickMsg('facilities')\" style='color: #007bff; text-decoration: underline;'>🏊 Facilities</a><br>
                <a href='#' onclick=\"sendQuickMsg('booking steps')\" style='color: #007bff; text-decoration: underline;'>📝 Booking Steps</a>";
    }
}