<?php

class WhatsappHelper
{
    public static function generateWhatsAppLink($name, $email, $phone, $message)
    {
        $whatsappNumber = '601140471172'; // Guest house owner's WhatsApp

        $text = urlencode(
            "📩 Welcome to Cherating Guest House!\n\nPlease find the submitted details:\n\n" .
            "👤 Name: $name\n" .
            "📧 Email: $email\n" .
            "📱 Phone: $phone\n" .
            "💬 Message: $message"
        );

        return "https://wa.me/$whatsappNumber?text=$text";
    }
}