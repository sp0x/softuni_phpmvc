<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170429235747 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product ADD availability_id INT DEFAULT NULL, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD61778466 FOREIGN KEY (availability_id) REFERENCES product_availability (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD61778466 ON product (availability_id)');
        $this->addSql('ALTER TABLE product_availability CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_availability ADD CONSTRAINT FK_B21380D44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B21380D44584665A ON product_availability (product_id)');
        $this->addSql('ALTER TABLE sale CHANGE created_on created_on DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD61778466');
        $this->addSql('DROP INDEX UNIQ_D34A04AD61778466 ON product');
        $this->addSql('ALTER TABLE product DROP availability_id, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product_availability DROP FOREIGN KEY FK_B21380D44584665A');
        $this->addSql('DROP INDEX UNIQ_B21380D44584665A ON product_availability');
        $this->addSql('ALTER TABLE product_availability CHANGE product_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE sale CHANGE created_on created_on DATETIME NOT NULL');
    }
}
