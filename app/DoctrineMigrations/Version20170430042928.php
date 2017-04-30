<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170430042928 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('DROP INDEX UNIQ_E54BC005558FBEB9 ON sale');
        $this->addSql('ALTER TABLE sale CHANGE purchase_id purchase_id VARCHAR(255) NOT NULL, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE sale CHANGE purchase_id purchase_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E54BC005558FBEB9 ON sale (purchase_id)');
    }
}
