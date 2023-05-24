<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404124238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip ADD guide_trip_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B91AAB426 FOREIGN KEY (guide_trip_id) REFERENCES guide (id)');
        $this->addSql('CREATE INDEX IDX_7656F53B91AAB426 ON trip (guide_trip_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B91AAB426');
        $this->addSql('DROP INDEX IDX_7656F53B91AAB426 ON trip');
        $this->addSql('ALTER TABLE trip DROP guide_trip_id');
    }
}
