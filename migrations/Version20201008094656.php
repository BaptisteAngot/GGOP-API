<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201008094656 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_75C949705E237E06 ON riot_server (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_75C94970265174A9 ON riot_server (api_route)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_75C949705E237E06 ON riot_server');
        $this->addSql('DROP INDEX UNIQ_75C94970265174A9 ON riot_server');
    }
}
