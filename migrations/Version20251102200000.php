<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create fleet_sets table
 */
final class Version20251102200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create fleet_sets table with relationships to trucks and trailers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE fleet_sets (
            id BLOB NOT NULL --(DC2Type:uuid)
            , truck_id BLOB NOT NULL --(DC2Type:uuid)
            , trailer_id BLOB NOT NULL --(DC2Type:uuid)
            , name VARCHAR(100) NOT NULL
            , created_at DATETIME NOT NULL
            , updated_at DATETIME NOT NULL
            , PRIMARY KEY(id)
            , CONSTRAINT FK_fleet_sets_truck FOREIGN KEY (truck_id) REFERENCES trucks (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            , CONSTRAINT FK_fleet_sets_trailer FOREIGN KEY (trailer_id) REFERENCES trailers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE INDEX IDX_fleet_sets_truck ON fleet_sets (truck_id)');
        $this->addSql('CREATE INDEX IDX_fleet_sets_trailer ON fleet_sets (trailer_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE fleet_sets');
    }
}

