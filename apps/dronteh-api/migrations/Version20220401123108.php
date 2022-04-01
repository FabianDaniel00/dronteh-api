<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220401123108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE area_of_use_chemical');
        $this->addSql('ALTER TABLE reservation CHANGE gps_coordinates gps_coordinates LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area_of_use_chemical (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, chemical_id INT NOT NULL, INDEX IDX_1EA97C3F1D935652 (plant_id), INDEX IDX_1EA97C3FE1770A76 (chemical_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE area_of_use_chemical ADD CONSTRAINT FK_AD764B4A1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE area_of_use_chemical ADD CONSTRAINT FK_AD764B4AE1770A76 FOREIGN KEY (chemical_id) REFERENCES chemical (id)');
        $this->addSql('ALTER TABLE reservation CHANGE gps_coordinates gps_coordinates VARCHAR(100) NOT NULL');
    }
}
