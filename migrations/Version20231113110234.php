<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113110234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD creation_date DATE NOT NULL');
        $this->addSql('ALTER TABLE trip ADD creation_date DATE NOT NULL');
        $this->addSql('ALTER TABLE user ADD creation_date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP creation_date');
        $this->addSql('ALTER TABLE trip DROP creation_date');
        $this->addSql('ALTER TABLE user DROP creation_date');
    }
}
