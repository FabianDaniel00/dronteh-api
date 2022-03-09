<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220308192742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plant CHANGE name_hu name_hu VARCHAR(255) NOT NULL, CHANGE name_sr_latn name_sr_latn VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE status status SMALLINT DEFAULT 1 NOT NULL, CHANGE reservation_interval_start reservation_interval_start DATE NOT NULL, CHANGE reservation_interval_end reservation_interval_end DATE NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE tel tel VARCHAR(20) NOT NULL, CHANGE last_verification_email_sent last_verification_email_sent DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plant CHANGE name_hu name_hu VARCHAR(255) DEFAULT NULL, CHANGE name_sr_latn name_sr_latn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE status status SMALLINT DEFAULT 0 NOT NULL, CHANGE reservation_interval_start reservation_interval_start DATE DEFAULT NULL, CHANGE reservation_interval_end reservation_interval_end DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE tel tel VARCHAR(20) DEFAULT NULL, CHANGE last_verification_email_sent last_verification_email_sent DATETIME DEFAULT NULL');
    }
}
