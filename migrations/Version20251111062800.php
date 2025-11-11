<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251111062800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create schedules table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE schedules (
                id SERIAL PRIMARY KEY,
                day DATE NOT NULL,
                start_time TIME(0) WITHOUT TIME ZONE NOT NULL,
                end_time TIME(0) WITHOUT TIME ZONE NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE schedules');
    }
}
