<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220302135250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemical CHANGE name_sr name_sr_latn VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE name_sr name_sr_latn VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chemical CHANGE name_sr_latn name_sr VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE name_sr_latn name_sr VARCHAR(255) DEFAULT NULL');
    }
}
