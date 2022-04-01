<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331192521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE drone_data_per_reservation');
        $this->addSql('ALTER TABLE reservation ADD results VARCHAR(5000) DEFAULT NULL, ADD chemical_quantity_per_ha DOUBLE PRECISION DEFAULT NULL, ADD water_quantity DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE drone_data_per_reservation (id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, results VARCHAR(5000) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, chemical_quantity_per_ha DOUBLE PRECISION NOT NULL, water_quantity DOUBLE PRECISION NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_389328F3B83297E7 (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE drone_data_per_reservation ADD CONSTRAINT FK_389328F3B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation DROP results, DROP chemical_quantity_per_ha, DROP water_quantity');
    }
}
