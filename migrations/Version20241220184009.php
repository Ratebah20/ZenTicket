<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241220184009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur ADD rapport_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E81DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32EB52E81DFBCC46 ON administrateur (rapport_id)');
        $this->addSql('ALTER TABLE chatbox ADD ia_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chatbox ADD CONSTRAINT FK_7472FC2F489A6E65 FOREIGN KEY (ia_id) REFERENCES ia (id)');
        $this->addSql('CREATE INDEX IDX_7472FC2F489A6E65 ON chatbox (ia_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E81DFBCC46');
        $this->addSql('DROP INDEX UNIQ_32EB52E81DFBCC46 ON administrateur');
        $this->addSql('ALTER TABLE administrateur DROP rapport_id');
        $this->addSql('ALTER TABLE chatbox DROP FOREIGN KEY FK_7472FC2F489A6E65');
        $this->addSql('DROP INDEX IDX_7472FC2F489A6E65 ON chatbox');
        $this->addSql('ALTER TABLE chatbox DROP ia_id');
    }
}
