<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406115409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, comments_details_id INT NOT NULL, login_comments_id INT NOT NULL, comments VARCHAR(255) NOT NULL, INDEX IDX_5F9E962AFE86A061 (comments_details_id), INDEX IDX_5F9E962AD1472936 (login_comments_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friend_list (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, friends_id INT NOT NULL, INDEX IDX_DEB224F8A76ED395 (user_id), INDEX IDX_DEB224F849CA8337 (friends_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friend_request (id INT AUTO_INCREMENT NOT NULL, from_request_id INT NOT NULL, to_request_id INT NOT NULL, request_status INT NOT NULL, INDEX IDX_F284D9469DA7EA2 (from_request_id), INDEX IDX_F284D94654D5AD8 (to_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE like_dislike (id INT AUTO_INCREMENT NOT NULL, post_del_id INT NOT NULL, like_dislike_id INT NOT NULL, th_up VARCHAR(255) NOT NULL, th_down VARCHAR(255) NOT NULL, INDEX IDX_ADB6A689ABA7A42E (post_del_id), INDEX IDX_ADB6A68952FAE844 (like_dislike_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE login (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, status INT NOT NULL, img VARCHAR(255) NOT NULL, about VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE otp (id INT AUTO_INCREMENT NOT NULL, email_id VARCHAR(255) NOT NULL, otp INT NOT NULL, validity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, details_id INT NOT NULL, post_details VARCHAR(255) NOT NULL, thums_up INT DEFAULT NULL, thums_down INT DEFAULT NULL, INDEX IDX_885DBAFABB1A0722 (details_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, hii VARCHAR(255) DEFAULT NULL, hell INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AFE86A061 FOREIGN KEY (comments_details_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AD1472936 FOREIGN KEY (login_comments_id) REFERENCES login (id)');
        $this->addSql('ALTER TABLE friend_list ADD CONSTRAINT FK_DEB224F8A76ED395 FOREIGN KEY (user_id) REFERENCES login (id)');
        $this->addSql('ALTER TABLE friend_list ADD CONSTRAINT FK_DEB224F849CA8337 FOREIGN KEY (friends_id) REFERENCES login (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D9469DA7EA2 FOREIGN KEY (from_request_id) REFERENCES login (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94654D5AD8 FOREIGN KEY (to_request_id) REFERENCES login (id)');
        $this->addSql('ALTER TABLE like_dislike ADD CONSTRAINT FK_ADB6A689ABA7A42E FOREIGN KEY (post_del_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE like_dislike ADD CONSTRAINT FK_ADB6A68952FAE844 FOREIGN KEY (like_dislike_id) REFERENCES login (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFABB1A0722 FOREIGN KEY (details_id) REFERENCES login (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE parking');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE parking (id INT AUTO_INCREMENT NOT NULL, vehicle_number VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, time_of_entry DATETIME NOT NULL, time_of_exit DATETIME DEFAULT NULL, status VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, vehicle_type VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AFE86A061');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AD1472936');
        $this->addSql('ALTER TABLE friend_list DROP FOREIGN KEY FK_DEB224F8A76ED395');
        $this->addSql('ALTER TABLE friend_list DROP FOREIGN KEY FK_DEB224F849CA8337');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D9469DA7EA2');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94654D5AD8');
        $this->addSql('ALTER TABLE like_dislike DROP FOREIGN KEY FK_ADB6A689ABA7A42E');
        $this->addSql('ALTER TABLE like_dislike DROP FOREIGN KEY FK_ADB6A68952FAE844');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFABB1A0722');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE friend_list');
        $this->addSql('DROP TABLE friend_request');
        $this->addSql('DROP TABLE like_dislike');
        $this->addSql('DROP TABLE login');
        $this->addSql('DROP TABLE otp');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE test');
    }
}
