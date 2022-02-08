<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211230120156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chemicals_of_land');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558C9E07DF');
        $this->addSql('DROP INDEX IDX_42C849558C9E07DF ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE plant_id_id plant_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849551D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('CREATE INDEX IDX_42C849551D935652 ON reservation (plant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chemicals_of_land (id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, chemical_id INT NOT NULL, is_deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_B00BB442B83297E7 (reservation_id), INDEX IDX_B00BB442E1770A76 (chemical_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE chemicals_of_land ADD CONSTRAINT FK_B00BB442E1770A76 FOREIGN KEY (chemical_id) REFERENCES chemical (id)');
        $this->addSql('ALTER TABLE chemicals_of_land ADD CONSTRAINT FK_B00BB442B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849551D935652');
        $this->addSql('DROP INDEX IDX_42C849551D935652 ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE plant_id plant_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558C9E07DF FOREIGN KEY (plant_id_id) REFERENCES plant (id)');
        $this->addSql('CREATE INDEX IDX_42C849558C9E07DF ON reservation (plant_id_id)');
    }
}
