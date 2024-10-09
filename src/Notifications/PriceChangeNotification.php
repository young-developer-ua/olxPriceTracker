<?php

namespace Src\Notifications;

class PriceChangeNotification
{
    public function sendPriceChangeNotification($email, $url, $price)
    {
        $subject = "The price of the product has changed!";
        $message = "The ad price for the $url link has changed. New price: $price UAH.";
        mail($email, $subject, $message);
    }
}
