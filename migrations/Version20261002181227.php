<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002181227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop address column from contact table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP COLUMN address');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact ADD COLUMN address CLOB DEFAULT NULL');
    }
}
