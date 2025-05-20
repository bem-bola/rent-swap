<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250517213932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email ADD device_id BIGINT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email ADD CONSTRAINT FK_E7927C7494A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E7927C7494A4C7D4 ON email (device_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email DROP FOREIGN KEY FK_E7927C7494A4C7D4
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E7927C7494A4C7D4 ON email
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email DROP device_id
        SQL);
    }
}
