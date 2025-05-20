<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250517160628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, sender_id BIGINT DEFAULT NULL, receiver_id BIGINT DEFAULT NULL, content LONGTEXT NOT NULL, adress_sender VARCHAR(255) DEFAULT NULL, INDEX IDX_E7927C74F624B39D (sender_id), INDEX IDX_E7927C74CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email ADD CONSTRAINT FK_E7927C74F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email ADD CONSTRAINT FK_E7927C74CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD created DATETIME NOT NULL, ADD deleted DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE type_id type_id BIGINT DEFAULT NULL, CHANGE is_verified is_verified TINYINT(1) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email DROP FOREIGN KEY FK_E7927C74F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email DROP FOREIGN KEY FK_E7927C74CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE email
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP created, DROP deleted
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE type_id type_id BIGINT NOT NULL, CHANGE is_verified is_verified TINYINT(1) NOT NULL
        SQL);
    }
}
