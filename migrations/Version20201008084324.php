<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201008084324 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ban (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, INDEX IDX_62FED0E59D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE riot_server (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, api_route VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ban ADD CONSTRAINT FK_62FED0E59D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD riot_server_id_id INT DEFAULT NULL, ADD pseudo VARCHAR(255) NOT NULL, ADD is_banned TINYINT(1) NOT NULL, ADD riot_pseudo VARCHAR(255) NOT NULL, ADD riot_account_id VARCHAR(255) NOT NULL, ADD is_riot_validated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F2C24E05 FOREIGN KEY (riot_server_id_id) REFERENCES riot_server (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F2C24E05 ON user (riot_server_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F2C24E05');
        $this->addSql('DROP TABLE ban');
        $this->addSql('DROP TABLE riot_server');
        $this->addSql('DROP INDEX IDX_8D93D649F2C24E05 ON user');
        $this->addSql('ALTER TABLE user DROP riot_server_id_id, DROP pseudo, DROP is_banned, DROP riot_pseudo, DROP riot_account_id, DROP is_riot_validated');
    }
}
