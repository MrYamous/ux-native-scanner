<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260916164818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove job_title column from contact table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP COLUMN job_title');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact ADD COLUMN job_title VARCHAR(255) DEFAULT NULL');
    }
}
