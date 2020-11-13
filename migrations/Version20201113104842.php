<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201113104842 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Embedded ForgottenPassword to User';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD forgotten_password_token CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD forgotten_password_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E15136F ON user (forgotten_password_token)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D6495E15136F ON user');
        $this->addSql('ALTER TABLE user DROP forgotten_password_token, DROP forgotten_password_requested_at');
    }
}
