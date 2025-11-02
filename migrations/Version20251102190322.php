<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251102190322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create drivers table with UUID, first name, last name, license number and timestamps';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE drivers (id BLOB NOT NULL --(DC2Type:uuid)
        , first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, license_number VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E410C307EC7E7152 ON drivers (license_number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE drivers');
    }
}
