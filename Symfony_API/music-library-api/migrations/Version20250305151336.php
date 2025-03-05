<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305151336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_album_created_by');
        $this->addSql('DROP INDEX FK_album_created_by ON album');
        $this->addSql('ALTER TABLE album CHANGE created_by created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_39986E43B03A8386 ON album (created_by_id)');
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_artist_created_by');
        $this->addSql('DROP INDEX FK_artist_created_by ON artist');
        $this->addSql('ALTER TABLE artist CHANGE created_by created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1599687B03A8386 ON artist (created_by_id)');
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_song_created_by');
        $this->addSql('DROP INDEX FK_song_created_by ON song');
        $this->addSql('ALTER TABLE song CHANGE created_by created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_33EDEEA1B03A8386 ON song (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43B03A8386');
        $this->addSql('DROP INDEX IDX_39986E43B03A8386 ON album');
        $this->addSql('ALTER TABLE album CHANGE created_by_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_album_created_by FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX FK_album_created_by ON album (created_by)');
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687B03A8386');
        $this->addSql('DROP INDEX IDX_1599687B03A8386 ON artist');
        $this->addSql('ALTER TABLE artist CHANGE created_by_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_artist_created_by FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX FK_artist_created_by ON artist (created_by)');
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA1B03A8386');
        $this->addSql('DROP INDEX IDX_33EDEEA1B03A8386 ON song');
        $this->addSql('ALTER TABLE song CHANGE created_by_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_song_created_by FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX FK_song_created_by ON song (created_by)');
    }
}
