<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308182909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chatbox ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chatbox ADD CONSTRAINT FK_7472FC2FA76ED395 FOREIGN KEY (user_id) REFERENCES personne (id)');
        $this->addSql('CREATE INDEX IDX_7472FC2FA76ED395 ON chatbox (user_id)');
        $this->addSql('ALTER TABLE technicien CHANGE specialite specialite VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chatbox DROP FOREIGN KEY FK_7472FC2FA76ED395');
        $this->addSql('DROP INDEX IDX_7472FC2FA76ED395 ON chatbox');
        $this->addSql('ALTER TABLE chatbox DROP user_id');
        $this->addSql('ALTER TABLE technicien CHANGE specialite specialite VARCHAR(255) DEFAULT NULL');
    }
}
