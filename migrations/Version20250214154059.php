<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214154059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sub_category (id BIGINT AUTO_INCREMENT NOT NULL, categories_id BIGINT DEFAULT NULL, name VARCHAR(150) NOT NULL, INDEX IDX_BCE3F798A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_category_device (sub_category_id BIGINT NOT NULL, device_id BIGINT NOT NULL, INDEX IDX_1C7C0CDAF7BFE87C (sub_category_id), INDEX IDX_1C7C0CDA94A4C7D4 (device_id), PRIMARY KEY(sub_category_id, device_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_category ADD CONSTRAINT FK_BCE3F798A21214B7 FOREIGN KEY (categories_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE sub_category_device ADD CONSTRAINT FK_1C7C0CDAF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_category_device ADD CONSTRAINT FK_1C7C0CDA94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_device DROP FOREIGN KEY FK_6C77CF512469DE2');
        $this->addSql('ALTER TABLE category_device DROP FOREIGN KEY FK_6C77CF594A4C7D4');
        $this->addSql('DROP TABLE category_device');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_device (category_id BIGINT NOT NULL, device_id BIGINT NOT NULL, INDEX IDX_6C77CF512469DE2 (category_id), INDEX IDX_6C77CF594A4C7D4 (device_id), PRIMARY KEY(category_id, device_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE category_device ADD CONSTRAINT FK_6C77CF512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_device ADD CONSTRAINT FK_6C77CF594A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_category DROP FOREIGN KEY FK_BCE3F798A21214B7');
        $this->addSql('ALTER TABLE sub_category_device DROP FOREIGN KEY FK_1C7C0CDAF7BFE87C');
        $this->addSql('ALTER TABLE sub_category_device DROP FOREIGN KEY FK_1C7C0CDA94A4C7D4');
        $this->addSql('DROP TABLE sub_category');
        $this->addSql('DROP TABLE sub_category_device');
    }
}
