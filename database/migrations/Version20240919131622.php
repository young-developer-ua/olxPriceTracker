<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240919131622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation of the subscribers table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE subscribers (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP TABLE subscribers
        ');
    }
}
