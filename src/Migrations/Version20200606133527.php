<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200606133527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(45) NOT NULL, UNIQUE INDEX UNIQ_3AF346685E237E06 (name), INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, video_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962A29C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriptions (id INT AUTO_INCREMENT NOT NULL, plan VARCHAR(255) NOT NULL, valid_to DATETIME NOT NULL, payment_status VARCHAR(45) DEFAULT NULL, free_plan_used TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, subscription_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(45) NOT NULL, last_name VARCHAR(45) NOT NULL, vimeo_api_key VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E99A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videos (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, INDEX IDX_29AA643212469DE2 (category_id), INDEX title_idx (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE likes (video_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_49CA4E7D29C1004E (video_id), INDEX IDX_49CA4E7DA76ED395 (user_id), PRIMARY KEY(video_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dislikes (video_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2DF3BE1129C1004E (video_id), INDEX IDX_2DF3BE11A76ED395 (user_id), PRIMARY KEY(video_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A29C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E99A1887DC FOREIGN KEY (subscription_id) REFERENCES subscriptions (id)');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA643212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D29C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dislikes ADD CONSTRAINT FK_2DF3BE1129C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dislikes ADD CONSTRAINT FK_2DF3BE11A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA643212469DE2');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E99A1887DC');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DA76ED395');
        $this->addSql('ALTER TABLE dislikes DROP FOREIGN KEY FK_2DF3BE11A76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A29C1004E');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D29C1004E');
        $this->addSql('ALTER TABLE dislikes DROP FOREIGN KEY FK_2DF3BE1129C1004E');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE videos');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE dislikes');
    }
}
