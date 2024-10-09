<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240919184412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation of the prices table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE prices (
                id INT AUTO_INCREMENT NOT NULL,
                product_id INT NOT NULL,
                price DOUBLE PRECISION NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('
            ALTER TABLE prices ADD CONSTRAINT FK_prices_product FOREIGN KEY (product_id) REFERENCES products(id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE prices DROP FOREIGN KEY FK_product
        ');

        $this->addSql('
            DROP TABLE prices
        ');
    }
}
