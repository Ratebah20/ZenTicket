<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223131709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT NOT NULL, rapport_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_32EB52E81DFBCC46 (rapport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, administrateur_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_497DD6347EE5403C (administrateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chatbox (id INT AUTO_INCREMENT NOT NULL, ia_id INT DEFAULT NULL, INDEX IDX_7472FC2F489A6E65 (ia_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, auteur_id INT NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, piece_jointe VARCHAR(255) DEFAULT NULL, INDEX IDX_67F068BC700047D2 (ticket_id), INDEX IDX_67F068BC60BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, s_nmp_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, INDEX IDX_B8B4C6F33303B12C (s_nmp_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ia (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, api_key VARCHAR(255) NOT NULL, model VARCHAR(50) NOT NULL, temperature DOUBLE PRECISION NOT NULL, default_context LONGTEXT DEFAULT NULL, additional_params JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, destinataire VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_5126AC48FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, chatbox_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, message_type VARCHAR(20) NOT NULL, reactions JSON DEFAULT NULL, is_read TINYINT(1) NOT NULL, sender_id INT NOT NULL, INDEX IDX_B6BD307F53527A38 (chatbox_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, ticket_id INT NOT NULL, titre VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, lu TINYINT(1) NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_BF5476CAFB88E14F (utilisateur_id), INDEX IDX_BF5476CA700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FCEC9EFE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rapport (id INT AUTO_INCREMENT NOT NULL, ticket_principal_id INT DEFAULT NULL, auteur_id INT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, type VARCHAR(50) NOT NULL, periode VARCHAR(50) DEFAULT NULL, service VARCHAR(50) NOT NULL, statistiques JSON DEFAULT NULL, temps_passe INT DEFAULT NULL, recommandations LONGTEXT DEFAULT NULL, INDEX IDX_BE34A09C879E8C45 (ticket_principal_id), INDEX IDX_BE34A09C60BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rapport_ticket (rapport_id INT NOT NULL, ticket_id INT NOT NULL, INDEX IDX_F6EB60691DFBCC46 (rapport_id), INDEX IDX_F6EB6069700047D2 (ticket_id), PRIMARY KEY(rapport_id, ticket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snmp (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE technicien (id INT NOT NULL, specialite VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, technicien_id INT DEFAULT NULL, categorie_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, statut VARCHAR(50) NOT NULL, priorite VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL, date_resolution DATETIME DEFAULT NULL, date_cloture DATETIME DEFAULT NULL, solution LONGTEXT DEFAULT NULL, solution_validee TINYINT(1) NOT NULL, INDEX IDX_97A0ADA3FB88E14F (utilisateur_id), INDEX IDX_97A0ADA313457256 (technicien_id), INDEX IDX_97A0ADA3BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E81DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id)');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8BF396750 FOREIGN KEY (id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD6347EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE chatbox ADD CONSTRAINT FK_7472FC2F489A6E65 FOREIGN KEY (ia_id) REFERENCES ia (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F33303B12C FOREIGN KEY (s_nmp_id) REFERENCES snmp (id)');
        $this->addSql('ALTER TABLE mail ADD CONSTRAINT FK_5126AC48FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F53527A38 FOREIGN KEY (chatbox_id) REFERENCES chatbox (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09C879E8C45 FOREIGN KEY (ticket_principal_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09C60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE rapport_ticket ADD CONSTRAINT FK_F6EB60691DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rapport_ticket ADD CONSTRAINT FK_F6EB6069700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE technicien ADD CONSTRAINT FK_96282C4CBF396750 FOREIGN KEY (id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA313457256 FOREIGN KEY (technicien_id) REFERENCES technicien (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3BF396750 FOREIGN KEY (id) REFERENCES personne (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E81DFBCC46');
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8BF396750');
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD6347EE5403C');
        $this->addSql('ALTER TABLE chatbox DROP FOREIGN KEY FK_7472FC2F489A6E65');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC700047D2');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F33303B12C');
        $this->addSql('ALTER TABLE mail DROP FOREIGN KEY FK_5126AC48FB88E14F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F53527A38');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAFB88E14F');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA700047D2');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C879E8C45');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C60BB6FE6');
        $this->addSql('ALTER TABLE rapport_ticket DROP FOREIGN KEY FK_F6EB60691DFBCC46');
        $this->addSql('ALTER TABLE rapport_ticket DROP FOREIGN KEY FK_F6EB6069700047D2');
        $this->addSql('ALTER TABLE technicien DROP FOREIGN KEY FK_96282C4CBF396750');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FB88E14F');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA313457256');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3BCF5E72D');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3BF396750');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE chatbox');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE ia');
        $this->addSql('DROP TABLE mail');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE rapport');
        $this->addSql('DROP TABLE rapport_ticket');
        $this->addSql('DROP TABLE snmp');
        $this->addSql('DROP TABLE technicien');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
