<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add fleet_set_id foreign key to drivers table (SQLite requires table recreation)
 */
final class Version20251102200001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add fleet_set_id foreign key to drivers table';
    }

    public function up(Schema $schema): void
    {
        // SQLite doesn't support ALTER TABLE for foreign keys, so we need to recreate the table
        
        // 1. Copy data to temporary storage
        $this->addSql('CREATE TEMPORARY TABLE drivers_backup AS SELECT * FROM drivers');
        
        // 2. Drop old table (this removes all indexes too)
        $this->addSql('DROP TABLE drivers');
        
        // 3. Create new table with foreign key
        $this->addSql('CREATE TABLE drivers (
            id BLOB NOT NULL --(DC2Type:uuid)
            , fleet_set_id BLOB DEFAULT NULL --(DC2Type:uuid)
            , first_name VARCHAR(100) NOT NULL
            , last_name VARCHAR(100) NOT NULL
            , license_number VARCHAR(50) NOT NULL
            , created_at DATETIME NOT NULL
            , updated_at DATETIME NOT NULL
            , PRIMARY KEY(id)
            , CONSTRAINT FK_drivers_fleet_set FOREIGN KEY (fleet_set_id) REFERENCES fleet_sets (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E410C307EC7E7152 ON drivers (license_number)');
        $this->addSql('CREATE INDEX IDX_drivers_fleet_set ON drivers (fleet_set_id)');
        
        // 4. Restore data from backup
        $this->addSql('INSERT INTO drivers (id, first_name, last_name, license_number, created_at, updated_at)
                       SELECT id, first_name, last_name, license_number, created_at, updated_at FROM drivers_backup');
        
        // 5. Drop temporary table
        $this->addSql('DROP TABLE drivers_backup');
    }

    public function down(Schema $schema): void
    {
        // Recreate drivers table without foreign key
        $this->addSql('CREATE TABLE drivers_old (
            id BLOB NOT NULL --(DC2Type:uuid)
            , first_name VARCHAR(100) NOT NULL
            , last_name VARCHAR(100) NOT NULL
            , license_number VARCHAR(50) NOT NULL
            , created_at DATETIME NOT NULL
            , updated_at DATETIME NOT NULL
            , PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E410C307EC7E7152 ON drivers_old (license_number)');
        
        // Copy data (fleet_set_id will be lost)
        $this->addSql('INSERT INTO drivers_old (id, first_name, last_name, license_number, created_at, updated_at)
                       SELECT id, first_name, last_name, license_number, created_at, updated_at FROM drivers');
        
        $this->addSql('DROP TABLE drivers');
        $this->addSql('ALTER TABLE drivers_old RENAME TO drivers');
    }
}

