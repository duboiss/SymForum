<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191118175506 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE thread ADD last_message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thread ADD CONSTRAINT FK_31204C83BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31204C83BA0E79C3 ON thread (last_message_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C83BA0E79C3');
        $this->addSql('DROP INDEX UNIQ_31204C83BA0E79C3 ON thread');
        $this->addSql('ALTER TABLE thread DROP last_message_id');
    }
}
