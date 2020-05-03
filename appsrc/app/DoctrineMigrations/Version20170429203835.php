<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170429203835 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE cost cost NUMERIC(10, 2) NOT NULL, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE sale CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE cash cash NUMERIC(10, 2) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE cost cost NUMERIC(2, 0) NOT NULL, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE sale CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE cash cash NUMERIC(10, 0) NOT NULL');
    }
}
