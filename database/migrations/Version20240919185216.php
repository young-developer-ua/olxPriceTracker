<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240919185216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation of the subscriptions table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE subscriptions (
                id INT AUTO_INCREMENT NOT NULL,
                subscriber_id INT NOT NULL,
                product_id INT NOT NULL,
                PRIMARY KEY(id)
           )
       ');

        $this->addSql('
            ALTER TABLE subscriptions ADD CONSTRAINT FK_subscriptions_subscriber FOREIGN KEY (subscriber_id) REFERENCES subscribers(id)
        ');

        $this->addSql('
            ALTER TABLE subscriptions ADD CONSTRAINT FK_subscriptions_product FOREIGN KEY (product_id) REFERENCES products(id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE subscriptions DROP FOREIGN KEY FK_subscriber
        ');
        $this->addSql('
            ALTER TABLE subscriptions DROP FOREIGN KEY FK_product
        ');
        $this->addSql('
            DROP TABLE subscriptions
        ');
    }
}
