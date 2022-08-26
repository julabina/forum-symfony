<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220826072516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE topic_responses (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', topic_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D8CA0B2C1F55203D (topic_id), INDEX IDX_D8CA0B2CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topics (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', sub_category_id INT NOT NULL, user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, sub_title VARCHAR(128) DEFAULT NULL, main_content LONGTEXT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_91F64639F7BFE87C (sub_category_id), INDEX IDX_91F64639A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE topic_responses ADD CONSTRAINT FK_D8CA0B2C1F55203D FOREIGN KEY (topic_id) REFERENCES topics (id)');
        $this->addSql('ALTER TABLE topic_responses ADD CONSTRAINT FK_D8CA0B2CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE topics ADD CONSTRAINT FK_91F64639F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_categories (id)');
        $this->addSql('ALTER TABLE topics ADD CONSTRAINT FK_91F64639A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE topic_responses DROP FOREIGN KEY FK_D8CA0B2C1F55203D');
        $this->addSql('ALTER TABLE topic_responses DROP FOREIGN KEY FK_D8CA0B2CA76ED395');
        $this->addSql('ALTER TABLE topics DROP FOREIGN KEY FK_91F64639F7BFE87C');
        $this->addSql('ALTER TABLE topics DROP FOREIGN KEY FK_91F64639A76ED395');
        $this->addSql('DROP TABLE topic_responses');
        $this->addSql('DROP TABLE topics');
        $this->addSql('ALTER TABLE users DROP created_at, DROP updated_at');
    }
}
