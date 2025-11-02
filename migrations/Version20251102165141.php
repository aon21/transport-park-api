<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251102165141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create trucks table with UUID, registration number, brand, model, status and timestamps';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trucks (id BLOB NOT NULL --(DC2Type:uuid)
        , registration_number VARCHAR(50) NOT NULL, brand VARCHAR(100) NOT NULL, model VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB16EAE638CEDFBE ON trucks (registration_number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE trucks');
    }
}
