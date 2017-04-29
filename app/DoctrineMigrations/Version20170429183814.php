<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170429183814 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, category_id INT DEFAULT NULL, is_general TINYINT(1) NOT NULL, start DATETIME NOT NULL, criteria LONGTEXT NOT NULL, ends DATETIME NOT NULL, discount INT NOT NULL, INDEX IDX_C11D7DD14584665A (product_id), INDEX IDX_C11D7DD112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product ADD image VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT NOT NULL, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE sale CHANGE created_on created_on DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE promotion');
        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product DROP image, DROP description, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE sale CHANGE created_on created_on DATETIME NOT NULL');
    }
}
