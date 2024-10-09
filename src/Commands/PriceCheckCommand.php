<?php

namespace Src\Commands;

use Src\Database;
use Src\Notifications\PriceChangeNotification;
use Src\Service\SubscriptionService;
use Symfony\Component\Console\Command\Command;

class PriceCheckCommand extends Command
{
    protected static $defaultName = 'app:check-prices';

    protected Database $db;
    protected PriceChangeNotification $emailService;
    protected SubscriptionService $subscriptionService;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database();
        $this->emailService = new PriceChangeNotification();
        $this->subscriptionService = new SubscriptionService();
    }

    public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $logFile = '/var/log/price_check.log';
        file_put_contents($logFile, "Price check started at: " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);

        $output->writeln('Checking prices...');
        $subscriptions = $this->getSubscriptions();
        $processedProducts = [];

        foreach ($subscriptions as $subscription) {
            $productId = $subscription['product_id'];

            if (in_array($productId, $processedProducts)) {
                continue;
            }

            $product = $this->getProduct($productId);
            $currentPrice = $this->getCurrentPrice($product['olx_url']);

            if ($currentPrice !== null) {
                $lastPrice = $this->getLastPrice($productId);

                if ($currentPrice !== $lastPrice) {
                    $subscribers = $this->getSubscribersByProduct($productId);

                    $this->notifySubscribers($subscribers, $product['olx_url'], $currentPrice);
                    $this->updatePrice($productId, $currentPrice);
                }
            }
        }

        file_put_contents($logFile, "Price check completed at: " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);

        return Command::SUCCESS;
    }

    protected function getSubscriptions()
    {
        $stmt = $this->db->getPDO()->prepare("SELECT * FROM subscriptions");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function getProduct($productId)
    {
        $stmt = $this->db->getPDO()->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    protected function getCurrentPrice($url)
    {
        return $this->subscriptionService->getPriceFromOlx($url);
    }

    protected function getLastPrice($productId)
    {
        $stmt = $this->db->getPDO()->prepare("SELECT price FROM prices WHERE product_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$productId]);
        return $stmt->fetchColumn();
    }

    protected function updatePrice($productId, $price)
    {
        $stmt = $this->db->getPDO()->prepare("INSERT INTO prices (product_id, price, created_at) VALUES (?, ?, ?)");
        $stmt->execute([$productId, $price, date('Y-m-d H:i:s')]);
    }

    protected function getSubscribersByProduct($productId)
    {
        $stmt = $this->db->getPDO()->prepare("SELECT s.id, s.email FROM subscribers s
                                              INNER JOIN subscriptions sub ON s.id = sub.subscriber_id
                                              WHERE sub.product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    protected function notifySubscribers($subscribers, $url, $price)
    {
        foreach ($subscribers as $subscriber) {
            $this->emailService->sendPriceChangeNotification($subscriber['email'], $url, $price);
        }
    }
}
