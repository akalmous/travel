<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230810145036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89A5BC2E0E');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BD7ED1D4B');
        $this->addSql('ALTER TABLE trip ADD archived TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BD7ED1D4B FOREIGN KEY (guide_id) REFERENCES guide (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BD7ED1D4B');
        $this->addSql('ALTER TABLE trip DROP archived');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BD7ED1D4B FOREIGN KEY (guide_id) REFERENCES guide (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89A5BC2E0E');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
