<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202131722 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE farm (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, adresse_adresse VARCHAR(255) DEFAULT NULL, adresse_rest_adresse VARCHAR(255) DEFAULT NULL, adresse_zip_code VARCHAR(5) DEFAULT NULL, adresse_city VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5816D045989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD farm_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64965FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64965FCFA0D ON user (farm_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64965FCFA0D');
        $this->addSql('DROP TABLE farm');
        $this->addSql('DROP INDEX IDX_8D93D64965FCFA0D ON user');
        $this->addSql('ALTER TABLE user DROP farm_id');
    }
}
