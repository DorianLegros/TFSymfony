<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201009114911 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CBBB7D3AD');
        $this->addSql('DROP INDEX IDX_6A2CA10CBBB7D3AD ON media');
        $this->addSql('ALTER TABLE media CHANGE commune_id_id commune_id INT NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C131A4F72 FOREIGN KEY (commune_id) REFERENCES town (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C131A4F72 ON media (commune_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C131A4F72');
        $this->addSql('DROP INDEX IDX_6A2CA10C131A4F72 ON media');
        $this->addSql('ALTER TABLE media CHANGE commune_id commune_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CBBB7D3AD FOREIGN KEY (commune_id_id) REFERENCES town (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10CBBB7D3AD ON media (commune_id_id)');
    }
}
