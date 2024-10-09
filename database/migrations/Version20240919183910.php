<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240919183910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation of the products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE products (
                id INT AUTO_INCREMENT NOT NULL, 
                olx_url VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP TABLE products
        ');
    }
}
