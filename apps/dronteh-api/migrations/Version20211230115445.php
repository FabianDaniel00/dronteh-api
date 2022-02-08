<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211230115445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE drone_data_per_reservation (id INT AUTO_INCREMENT NOT NULL, reservation_id_id INT NOT NULL, gps_coordinates VARCHAR(100) NOT NULL, results VARCHAR(5000) NOT NULL, chemical_quantity_per_ha DOUBLE PRECISION NOT NULL, water_quantity DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_389328F33C3B4EF0 (reservation_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE drone_data_per_reservation ADD CONSTRAINT FK_389328F33C3B4EF0 FOREIGN KEY (reservation_id_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE contact CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation ADD plant_id_id INT NOT NULL, DROP coordinates, DROP plant_type, DROP chemical_quantity_per_ha, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE time time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558C9E07DF FOREIGN KEY (plant_id_id) REFERENCES plant (id)');
        $this->addSql('CREATE INDEX IDX_42C849558C9E07DF ON reservation (plant_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558C9E07DF');
        $this->addSql('DROP TABLE drone_data_per_reservation');
        $this->addSql('DROP TABLE plant');
        $this->addSql('ALTER TABLE contact CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX IDX_42C849558C9E07DF ON reservation');
        $this->addSql('ALTER TABLE reservation ADD coordinates VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD plant_type VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD chemical_quantity_per_ha DOUBLE PRECISION NOT NULL, DROP plant_id_id, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE time time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
