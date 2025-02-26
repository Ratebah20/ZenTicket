<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223133949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket ADD chatbox_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA353527A38 FOREIGN KEY (chatbox_id) REFERENCES chatbox (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97A0ADA353527A38 ON ticket (chatbox_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA353527A38');
        $this->addSql('DROP INDEX UNIQ_97A0ADA353527A38 ON ticket');
        $this->addSql('ALTER TABLE ticket DROP chatbox_id');
    }
}
