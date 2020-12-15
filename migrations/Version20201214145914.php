<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201214145914 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Update Product entity with relationship ManyToOne to Farm entity';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD farm_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD description LONGTEXT NOT NULL, ADD quantity INT NOT NULL, ADD price_unit_price INT NOT NULL, ADD price_vat NUMERIC(5, 2) NOT NULL, ADD image_path VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD65FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D34A04AD65FCFA0D ON product (farm_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD65FCFA0D');
        $this->addSql('DROP INDEX IDX_D34A04AD65FCFA0D ON product');
        $this->addSql('ALTER TABLE product DROP farm_id, DROP description, DROP quantity, DROP price_unit_price, DROP price_vat, DROP image_path, CHANGE name name TINYTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
