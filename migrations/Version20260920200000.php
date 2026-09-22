<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add nullable city column to contact table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact ADD city VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP COLUMN city');
    }
}
