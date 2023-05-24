<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404122517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip ADD user_guide_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B3760F94B FOREIGN KEY (user_guide_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7656F53B3760F94B ON trip (user_guide_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B3760F94B');
        $this->addSql('DROP INDEX IDX_7656F53B3760F94B ON trip');
        $this->addSql('ALTER TABLE trip DROP user_guide_id');
    }
}
