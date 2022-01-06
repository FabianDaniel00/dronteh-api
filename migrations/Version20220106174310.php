<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220106174310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drone_data_per_reservation DROP FOREIGN KEY FK_389328F33C3B4EF0');
        $this->addSql('DROP INDEX UNIQ_389328F33C3B4EF0 ON drone_data_per_reservation');
        $this->addSql('ALTER TABLE drone_data_per_reservation CHANGE reservation_id_id reservation_id INT NOT NULL');
        $this->addSql('ALTER TABLE drone_data_per_reservation ADD CONSTRAINT FK_389328F3B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389328F3B83297E7 ON drone_data_per_reservation (reservation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drone_data_per_reservation DROP FOREIGN KEY FK_389328F3B83297E7');
        $this->addSql('DROP INDEX UNIQ_389328F3B83297E7 ON drone_data_per_reservation');
        $this->addSql('ALTER TABLE drone_data_per_reservation CHANGE reservation_id reservation_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE drone_data_per_reservation ADD CONSTRAINT FK_389328F33C3B4EF0 FOREIGN KEY (reservation_id_id) REFERENCES reservation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389328F33C3B4EF0 ON drone_data_per_reservation (reservation_id_id)');
    }
}
