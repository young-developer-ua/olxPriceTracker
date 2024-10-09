<?php

namespace Src\Service;

use PDOException;
use Src\Database;

class SubscriptionService
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createSubscription($email, $url): array
    {
        try {
            $pdo = $this->db->getPDO();

            $adPrice = $this->getPriceFromOlx($url);
            if ($adPrice === null) {
                return ['status' => 'error', 'message' => 'Failed to get price from OLX.'];
            }

            $productStatement = $pdo->prepare("SELECT id FROM products WHERE olx_url = ?");
            $productStatement->execute([$url]);
            $product = $productStatement->fetch();

            if (!$product) {
                $productStatement = $pdo->prepare("INSERT INTO products (olx_url) VALUES (?)");
                $productStatement->execute([$url]);
                $productId = $pdo->lastInsertId();
            } else {
                $productId = $product['id'];
            }

            $priceStatement = $pdo->prepare("SELECT price FROM prices WHERE product_id = ? ORDER BY created_at DESC LIMIT 1");
            $priceStatement->execute([$productId]);
            $price = $priceStatement->fetch();

            if (!$price) {
                $priceStatement = $pdo->prepare("INSERT INTO prices (product_id, price, created_at) VALUES (?, ?, ?)");
                $priceStatement->execute([$productId, $adPrice, date('Y-m-d H:i:s')]);
            }

            $subscriberStatement = $pdo->prepare("SELECT id FROM subscribers WHERE email = ?");
            $subscriberStatement->execute([$email]);
            $subscriber = $subscriberStatement->fetch();

            if (!$subscriber) {
                $subscriberStatement = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
                $subscriberStatement->execute([$email]);
                $subscriberId = $pdo->lastInsertId();
            } else {
                $subscriberId = $subscriber['id'];
            }

            $subscriptionStatement = $pdo->prepare("SELECT id FROM subscriptions WHERE subscriber_id = ? AND product_id = ?");
            $subscriptionStatement->execute([$subscriberId, $productId]);
            $existingSubscription = $subscriptionStatement->fetch();

            if (!$existingSubscription) {
                $existingSubscription = $pdo->prepare("INSERT INTO subscriptions (subscriber_id, product_id) VALUES (?, ?)");
                $existingSubscription->execute([$subscriberId, $productId]);
                return ['status' => 'success', 'message' => 'Subscription successfully created.'];
            } else {
                return ['status' => 'error', 'message' => 'You are already subscribed to this product.'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'A database error has occurred: ' . $e->getMessage()];
        }
    }

    public function getPriceFromOlx($url): ?float
    {
        $html = file_get_contents($url);

        preg_match('/<h3 class="css-90xrc0">([\d\s]+) ?(грн\.|\$)<\/h3>/', $html, $matches);

        return isset($matches[1]) ? floatval(str_replace(' ', '', $matches[1])) : null;
    }
}
