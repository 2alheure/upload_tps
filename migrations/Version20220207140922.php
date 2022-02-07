<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207140922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utps_exercice (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, subject_file LONGTEXT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, subject_link LONGTEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_406EC975AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utps_module (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utps_promo (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utps_render (id INT AUTO_INCREMENT NOT NULL, promo_id INT NOT NULL, exercice_id INT NOT NULL, date_begin DATETIME DEFAULT NULL, date_end DATETIME DEFAULT NULL, directory VARCHAR(255) NOT NULL, INDEX IDX_392F9E8ED0C07AFF (promo_id), INDEX IDX_392F9E8E89D40298 (exercice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utps_reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EF74CF66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utps_upload (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, render_id INT NOT NULL, render_file LONGTEXT DEFAULT NULL, render_link LONGTEXT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_BACEE1FBA76ED395 (user_id), INDEX IDX_BACEE1FBE15FA7DE (render_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utps_user (id INT AUTO_INCREMENT NOT NULL, promo_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_36DF355E7927C74 (email), INDEX IDX_36DF355D0C07AFF (promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE utps_exercice ADD CONSTRAINT FK_406EC975AFC2B591 FOREIGN KEY (module_id) REFERENCES utps_module (id)');
        $this->addSql('ALTER TABLE utps_render ADD CONSTRAINT FK_392F9E8ED0C07AFF FOREIGN KEY (promo_id) REFERENCES utps_promo (id)');
        $this->addSql('ALTER TABLE utps_render ADD CONSTRAINT FK_392F9E8E89D40298 FOREIGN KEY (exercice_id) REFERENCES utps_exercice (id)');
        $this->addSql('ALTER TABLE utps_reset_password_request ADD CONSTRAINT FK_EF74CF66A76ED395 FOREIGN KEY (user_id) REFERENCES utps_user (id)');
        $this->addSql('ALTER TABLE utps_upload ADD CONSTRAINT FK_BACEE1FBA76ED395 FOREIGN KEY (user_id) REFERENCES utps_user (id)');
        $this->addSql('ALTER TABLE utps_upload ADD CONSTRAINT FK_BACEE1FBE15FA7DE FOREIGN KEY (render_id) REFERENCES utps_render (id)');
        $this->addSql('ALTER TABLE utps_user ADD CONSTRAINT FK_36DF355D0C07AFF FOREIGN KEY (promo_id) REFERENCES utps_promo (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utps_render DROP FOREIGN KEY FK_392F9E8E89D40298');
        $this->addSql('ALTER TABLE utps_exercice DROP FOREIGN KEY FK_406EC975AFC2B591');
        $this->addSql('ALTER TABLE utps_render DROP FOREIGN KEY FK_392F9E8ED0C07AFF');
        $this->addSql('ALTER TABLE utps_user DROP FOREIGN KEY FK_36DF355D0C07AFF');
        $this->addSql('ALTER TABLE utps_upload DROP FOREIGN KEY FK_BACEE1FBE15FA7DE');
        $this->addSql('ALTER TABLE utps_reset_password_request DROP FOREIGN KEY FK_EF74CF66A76ED395');
        $this->addSql('ALTER TABLE utps_upload DROP FOREIGN KEY FK_BACEE1FBA76ED395');
        $this->addSql('DROP TABLE utps_exercice');
        $this->addSql('DROP TABLE utps_module');
        $this->addSql('DROP TABLE utps_promo');
        $this->addSql('DROP TABLE utps_render');
        $this->addSql('DROP TABLE utps_reset_password_request');
        $this->addSql('DROP TABLE utps_upload');
        $this->addSql('DROP TABLE utps_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
