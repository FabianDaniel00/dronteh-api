<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220223150726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE area_of_use_chemical DROP FOREIGN KEY FK_AD764B4AE1770A76');
        $this->addSql('ALTER TABLE area_of_use_chemical DROP FOREIGN KEY FK_AD764B4A1D935652');
        $this->addSql('DROP INDEX idx_ad764b4a1d935652 ON area_of_use_chemical');
        $this->addSql('CREATE INDEX IDX_1EA97C3F1D935652 ON area_of_use_chemical (plant_id)');
        $this->addSql('DROP INDEX idx_ad764b4ae1770a76 ON area_of_use_chemical');
        $this->addSql('CREATE INDEX IDX_1EA97C3FE1770A76 ON area_of_use_chemical (chemical_id)');
        $this->addSql('ALTER TABLE area_of_use_chemical ADD CONSTRAINT FK_AD764B4AE1770A76 FOREIGN KEY (chemical_id) REFERENCES chemical (id)');
        $this->addSql('ALTER TABLE area_of_use_chemical ADD CONSTRAINT FK_AD764B4A1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE user ADD last_verification_email_sent DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE area_of_use_chemical DROP FOREIGN KEY FK_1EA97C3F1D935652');
        $this->addSql('ALTER TABLE area_of_use_chemical DROP FOREIGN KEY FK_1EA97C3FE1770A76');
        $this->addSql('DROP INDEX idx_1ea97c3f1d935652 ON area_of_use_chemical');
        $this->addSql('CREATE INDEX IDX_AD764B4A1D935652 ON area_of_use_chemical (plant_id)');
        $this->addSql('DROP INDEX idx_1ea97c3fe1770a76 ON area_of_use_chemical');
        $this->addSql('CREATE INDEX IDX_AD764B4AE1770A76 ON area_of_use_chemical (chemical_id)');
        $this->addSql('ALTER TABLE area_of_use_chemical ADD CONSTRAINT FK_1EA97C3F1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE area_of_use_chemical ADD CONSTRAINT FK_1EA97C3FE1770A76 FOREIGN KEY (chemical_id) REFERENCES chemical (id)');
        $this->addSql('ALTER TABLE user DROP last_verification_email_sent');
    }
}
