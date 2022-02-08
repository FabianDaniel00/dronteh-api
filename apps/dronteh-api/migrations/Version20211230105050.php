<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211230105050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemicals_of_land DROP FOREIGN KEY FK_B00BB4421994904A');
        $this->addSql('DROP TABLE land_per_client');
        $this->addSql('DROP INDEX IDX_B00BB4421994904A ON chemicals_of_land');
        $this->addSql('ALTER TABLE chemicals_of_land CHANGE land_id reservation_id INT NOT NULL');
        $this->addSql('ALTER TABLE chemicals_of_land ADD CONSTRAINT FK_B00BB442B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE INDEX IDX_B00BB442B83297E7 ON chemicals_of_land (reservation_id)');
        $this->addSql('ALTER TABLE reservation ADD chemical_id INT NOT NULL, ADD parcel_number VARCHAR(100) NOT NULL, ADD land_area DOUBLE PRECISION NOT NULL, ADD chemical_quantity_per_ha DOUBLE PRECISION NOT NULL, ADD status SMALLINT NOT NULL, ADD to_be_present TINYINT(1) NOT NULL, ADD comment VARCHAR(5000) DEFAULT NULL, CHANGE coordinates coordinates VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E1770A76 FOREIGN KEY (chemical_id) REFERENCES chemical (id)');
        $this->addSql('CREATE INDEX IDX_42C84955E1770A76 ON reservation (chemical_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE land_per_client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, chemical_id INT NOT NULL, land_name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, land_area DOUBLE PRECISION NOT NULL, chemical_quantity_per_ha DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_581EC2F2E1770A76 (chemical_id), INDEX IDX_581EC2F2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE land_per_client ADD CONSTRAINT FK_581EC2F2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE land_per_client ADD CONSTRAINT FK_581EC2F2E1770A76 FOREIGN KEY (chemical_id) REFERENCES chemical (id)');
        $this->addSql('ALTER TABLE chemicals_of_land DROP FOREIGN KEY FK_B00BB442B83297E7');
        $this->addSql('DROP INDEX IDX_B00BB442B83297E7 ON chemicals_of_land');
        $this->addSql('ALTER TABLE chemicals_of_land CHANGE reservation_id land_id INT NOT NULL');
        $this->addSql('ALTER TABLE chemicals_of_land ADD CONSTRAINT FK_B00BB4421994904A FOREIGN KEY (land_id) REFERENCES land_per_client (id)');
        $this->addSql('CREATE INDEX IDX_B00BB4421994904A ON chemicals_of_land (land_id)');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E1770A76');
        $this->addSql('DROP INDEX IDX_42C84955E1770A76 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP chemical_id, DROP parcel_number, DROP land_area, DROP chemical_quantity_per_ha, DROP status, DROP to_be_present, DROP comment, CHANGE coordinates coordinates VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
