<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220330162615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemical CHANGE name_hu name_hu VARCHAR(255) NOT NULL, CHANGE name_sr_latn name_sr_latn VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE drone_data_per_reservation DROP gps_coordinates');
        $this->addSql('ALTER TABLE reservation ADD gps_coordinates VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE last_verification_email_sent last_verification_email_sent DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemical CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL, CHANGE name_sr_latn name_sr_latn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE drone_data_per_reservation ADD gps_coordinates VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP gps_coordinates');
        $this->addSql('ALTER TABLE user CHANGE last_verification_email_sent last_verification_email_sent DATETIME NOT NULL');
    }
}
