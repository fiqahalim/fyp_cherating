<?php

class ChatbotController extends Controller
{
    public function ask()
    {
        // 1. Get the raw POST data (from fetch API)
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $userMessage = $data['message'] ?? '';

        if (empty($userMessage)) {
            echo json_encode(['reply' => 'Please type something!']);
            return;
        }

        // 2. Call the Helper
        try {
            $aiReply = ChatbotHelper::getResponse($userMessage);
        } catch (Exception $e) {
            $aiReply = "I'm sorry, I'm having trouble thinking right now. Please try again later.";
        }

        // 3. Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['reply' => $aiReply]);
    }
}