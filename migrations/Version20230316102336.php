<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316102336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE guide (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(10000) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture_guide (id INT AUTO_INCREMENT NOT NULL, guide_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_4A44407BD7ED1D4B (guide_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE picture_guide ADD CONSTRAINT FK_4A44407BD7ED1D4B FOREIGN KEY (guide_id) REFERENCES guide (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture_guide DROP FOREIGN KEY FK_4A44407BD7ED1D4B');
        $this->addSql('DROP TABLE guide');
        $this->addSql('DROP TABLE picture_guide');
    }
}
