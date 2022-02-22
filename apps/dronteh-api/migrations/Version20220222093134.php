<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220222093134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemical ADD name_hu VARCHAR(255) DEFAULT NULL, ADD name_en VARCHAR(255) NOT NULL, ADD name_sr VARCHAR(255) DEFAULT NULL, DROP name');
        $this->addSql('ALTER TABLE plant ADD name_hu VARCHAR(255) DEFAULT NULL, ADD name_en VARCHAR(255) NOT NULL, ADD name_sr VARCHAR(255) DEFAULT NULL, DROP name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemical ADD name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP name_hu, DROP name_en, DROP name_sr');
        $this->addSql('ALTER TABLE plant ADD name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP name_hu, DROP name_en, DROP name_sr');
    }
}
