<?php

use PHPUnit\Framework\TestCase;
use Src\Service\SubscriptionService;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class SubscriptionServiceTest extends TestCase
{
    protected SubscriptionService $service;

    public static function setUpBeforeClass(): void
    {
        $application = new Application();
        $application->add(new \Doctrine\Migrations\Tools\Console\Command\MigrateCommand());

        $input = new ArrayInput([
            'command' => 'migrations:migrate',
            '--no-interaction' => true,
            '--configuration' => '../migrations.php'
        ]);
        $application->run($input);
    }

    protected function setUp(): void
    {
        $this->service = new SubscriptionService();
    }

    public function testCreateSubscriptionSuccess()
    {
        $this->service = $this->getMockBuilder(SubscriptionService::class)
            ->onlyMethods(['getPriceFromOlx'])
            ->getMock();

        $this->service->method('getPriceFromOlx')
            ->willReturn(123.4);

        $result = $this->service->createSubscription('test1@example.com', 'https://example.com/adtest1');
        $this->assertEquals('success', $result['status'], $result['message']);
    }

    public function testCreateSubscriptionDuplicate()
    {
        $this->service = $this->getMockBuilder(SubscriptionService::class)
            ->onlyMethods(['getPriceFromOlx'])
            ->getMock();

        $this->service->method('getPriceFromOlx')
            ->willReturn(123.4);

        $this->service->createSubscription('test2@example.com', 'https://example.com/adtest2');

        $result = $this->service->createSubscription('test2@example.com', 'https://example.com/adtest2');
        $this->assertEquals('error', $result['status']);
        $this->assertEquals('You are already subscribed to this product.', $result['message']);
    }

    public function testGetPriceFromOlxFailure()
    {
        $price = $this->service->getPriceFromOlx('https://invalid-url.com');
        $this->assertNull($price, "Expected null for invalid URL, got $price");
    }
}
