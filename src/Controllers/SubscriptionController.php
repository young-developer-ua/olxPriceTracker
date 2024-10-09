<?php

namespace Src\Controllers;

use Src\Service\SubscriptionService;

session_start();

class SubscriptionController
{
    protected $subscriptionService;

    public function __construct() {
        $this->subscriptionService = new SubscriptionService();
    }

    public function subscribe() {
        $email = $_POST['email'] ?? '';
        $url = $_POST['olx_url'] ?? '';

        $result = $this->subscriptionService->createSubscription($email, $url);

        if ($result['status'] === 'success') {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }

        header('Location: /');
        exit();
    }
}