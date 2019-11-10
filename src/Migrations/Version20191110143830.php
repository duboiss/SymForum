<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191110143830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_852BBECD989D9B62 ON forum (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31204C83989D9B62 ON thread (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649989D9B62 ON user (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FADA63E95E237E06 ON core_option (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_64C19C1989D9B62 ON category');
        $this->addSql('DROP INDEX UNIQ_FADA63E95E237E06 ON core_option');
        $this->addSql('DROP INDEX UNIQ_852BBECD989D9B62 ON forum');
        $this->addSql('DROP INDEX UNIQ_31204C83989D9B62 ON thread');
        $this->addSql('DROP INDEX UNIQ_8D93D649989D9B62 ON user');
    }
}
