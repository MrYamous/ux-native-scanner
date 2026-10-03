<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Add department and region columns to contact table
 */
final class Version20261003090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add department and region columns to contact table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact ADD department VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE contact ADD region VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP COLUMN department');
        $this->addSql('ALTER TABLE contact DROP COLUMN region');
    }
}
