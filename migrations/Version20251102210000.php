<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create orders table with optional foreign keys to trucks, trailers, and fleet_sets
 */
final class Version20251102210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders table with service order tracking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE orders (
            id BLOB NOT NULL,
            truck_id BLOB DEFAULT NULL,
            trailer_id BLOB DEFAULT NULL,
            fleet_set_id BLOB DEFAULT NULL,
            order_number VARCHAR(50) NOT NULL,
            service_type VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            status VARCHAR(20) NOT NULL,
            start_date DATETIME NOT NULL,
            end_date DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY (truck_id) REFERENCES trucks(id),
            FOREIGN KEY (trailer_id) REFERENCES trailers(id),
            FOREIGN KEY (fleet_set_id) REFERENCES fleet_sets(id)
        )');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE551F0F81 ON orders (order_number)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEC6957CCE ON orders (truck_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEECD847151 ON orders (trailer_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE8B08593C ON orders (fleet_set_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE7B00651C ON orders (status)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE orders');
    }
}

