<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214151956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alert (id BIGINT AUTO_INCREMENT NOT NULL, author_id BIGINT NOT NULL, message_id BIGINT DEFAULT NULL, notice_id BIGINT DEFAULT NULL, user_id BIGINT DEFAULT NULL, created DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_17FD46C1F675F31B (author_id), INDEX IDX_17FD46C1537A1329 (message_id), INDEX IDX_17FD46C17D540AB (notice_id), INDEX IDX_17FD46C1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_device (category_id BIGINT NOT NULL, device_id BIGINT NOT NULL, INDEX IDX_6C77CF512469DE2 (category_id), INDEX IDX_6C77CF594A4C7D4 (device_id), PRIMARY KEY(category_id, device_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, parent_id BIGINT DEFAULT NULL, slug VARCHAR(250) NOT NULL, body LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, show_phone TINYINT(1) DEFAULT NULL, created DATETIME NOT NULL, deleted DATETIME DEFAULT NULL, title VARCHAR(100) NOT NULL, status VARCHAR(10) NOT NULL, location VARCHAR(100) NOT NULL, phone_number VARCHAR(10) DEFAULT NULL, INDEX IDX_92FB68EA76ED395 (user_id), INDEX IDX_92FB68E727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_picture (id BIGINT AUTO_INCREMENT NOT NULL, device_id BIGINT NOT NULL, created DATETIME NOT NULL, deleted DATETIME DEFAULT NULL, INDEX IDX_9C6D961494A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, device_id BIGINT NOT NULL, INDEX IDX_68C58ED9A76ED395 (user_id), INDEX IDX_68C58ED994A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id BIGINT AUTO_INCREMENT NOT NULL, sender_id BIGINT NOT NULL, receiver_id BIGINT NOT NULL, content LONGTEXT NOT NULL, is_deleted TINYINT(1) DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FCD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notice (id BIGINT AUTO_INCREMENT NOT NULL, device_id BIGINT NOT NULL, user_id BIGINT NOT NULL, rate DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, content LONGTEXT DEFAULT NULL, is_deleted TINYINT(1) DEFAULT NULL, INDEX IDX_480D45C294A4C7D4 (device_id), INDEX IDX_480D45C2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan (id BIGINT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, stripe_id VARCHAR(255) NOT NULL, rate INT NOT NULL, created DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, device_id BIGINT NOT NULL, mean_rate DOUBLE PRECISION NOT NULL, INDEX IDX_D8892622A76ED395 (user_id), INDEX IDX_D889262294A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating_detail (id BIGINT AUTO_INCREMENT NOT NULL, parent_id BIGINT DEFAULT NULL, device_id BIGINT NOT NULL, rate DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, content LONGTEXT DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_1F4151B727ACA70 (parent_id), INDEX IDX_1F4151B94A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, status_id BIGINT NOT NULL, device_id BIGINT NOT NULL, price DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, ended DATETIME DEFAULT NULL, INDEX IDX_42C84955A76ED395 (user_id), INDEX IDX_42C849556BF700BD (status_id), INDEX IDX_42C8495594A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_status (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, plan_id BIGINT NOT NULL, stripe_id VARCHAR(255) NOT NULL, current_period_start DATETIME NOT NULL, current_period_end DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_A3C664D3A76ED395 (user_id), INDEX IDX_A3C664D3E899029B (plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_user (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BIGINT AUTO_INCREMENT NOT NULL, type_id BIGINT NOT NULL, created DATETIME NOT NULL, siret VARCHAR(30) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, is_deleted TINYINT(1) DEFAULT NULL, username VARCHAR(100) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, role JSON NOT NULL, INDEX IDX_8D93D649C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warn_user (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, description LONGTEXT NOT NULL, is_banned TINYINT(1) DEFAULT NULL, created DATETIME NOT NULL, started DATETIME DEFAULT NULL, ended DATETIME DEFAULT NULL, INDEX IDX_3117E4F0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C1F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C1537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C17D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category_device ADD CONSTRAINT FK_6C77CF512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_device ADD CONSTRAINT FK_6C77CF594A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E727ACA70 FOREIGN KEY (parent_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE device_picture ADD CONSTRAINT FK_9C6D961494A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED994A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C294A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D889262294A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE rating_detail ADD CONSTRAINT FK_1F4151B727ACA70 FOREIGN KEY (parent_id) REFERENCES rating_detail (id)');
        $this->addSql('ALTER TABLE rating_detail ADD CONSTRAINT FK_1F4151B94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556BF700BD FOREIGN KEY (status_id) REFERENCES reservation_status (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495594A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C54C8C93 FOREIGN KEY (type_id) REFERENCES type_user (id)');
        $this->addSql('ALTER TABLE warn_user ADD CONSTRAINT FK_3117E4F0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C1F675F31B');
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C1537A1329');
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C17D540AB');
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C1A76ED395');
        $this->addSql('ALTER TABLE category_device DROP FOREIGN KEY FK_6C77CF512469DE2');
        $this->addSql('ALTER TABLE category_device DROP FOREIGN KEY FK_6C77CF594A4C7D4');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EA76ED395');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E727ACA70');
        $this->addSql('ALTER TABLE device_picture DROP FOREIGN KEY FK_9C6D961494A4C7D4');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9A76ED395');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED994A4C7D4');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C294A4C7D4');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2A76ED395');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D889262294A4C7D4');
        $this->addSql('ALTER TABLE rating_detail DROP FOREIGN KEY FK_1F4151B727ACA70');
        $this->addSql('ALTER TABLE rating_detail DROP FOREIGN KEY FK_1F4151B94A4C7D4');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556BF700BD');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495594A4C7D4');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3E899029B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C54C8C93');
        $this->addSql('ALTER TABLE warn_user DROP FOREIGN KEY FK_3117E4F0A76ED395');
        $this->addSql('DROP TABLE alert');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_device');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE device_picture');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notice');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE rating_detail');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_status');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE type_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE warn_user');
    }
}
