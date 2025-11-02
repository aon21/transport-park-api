<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251102165556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create trailers table with UUID, registration number, type, capacity, status and timestamps';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trailers (id BLOB NOT NULL --(DC2Type:uuid)
        , registration_number VARCHAR(50) NOT NULL, type VARCHAR(100) NOT NULL, capacity NUMERIC(10, 2) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8AAC324038CEDFBE ON trailers (registration_number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE trailers');
    }
}
