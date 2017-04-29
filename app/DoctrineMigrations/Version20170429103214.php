<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170429103214 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, user_id INT DEFAULT NULL, quantity INT NOT NULL, status VARCHAR(255) NOT NULL, is_available TINYINT(1) NOT NULL, INDEX IDX_F0FE25274584665A (product_id), INDEX IDX_F0FE2527A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, user_id INT DEFAULT NULL, purchase_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', quantity INT NOT NULL, created_on DATETIME NOT NULL, UNIQUE INDEX UNIQ_E54BC005558FBEB9 (purchase_id), INDEX IDX_E54BC0054584665A (product_id), INDEX IDX_E54BC005A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0054584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC005A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE video');
        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product ADD `order` INT NOT NULL, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product_comment ADD user_id INT DEFAULT NULL, DROP author');
        $this->addSql('ALTER TABLE product_comment ADD CONSTRAINT FK_45AD49DCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_45AD49DCA76ED395 ON product_comment (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, video_id VARCHAR(500) NOT NULL COLLATE utf8_unicode_ci, title VARCHAR(1000) NOT NULL COLLATE utf8_unicode_ci, config LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, is_playlist TINYINT(1) DEFAULT NULL, image VARCHAR(600) DEFAULT NULL COLLATE utf8_unicode_ci, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, duration INT NOT NULL, available TINYINT(1) NOT NULL, author VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, upload_date DATETIME NOT NULL, items LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE sale');
        $this->addSql('ALTER TABLE host_ban CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product DROP `order`, CHANGE created_on created_on DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product_comment DROP FOREIGN KEY FK_45AD49DCA76ED395');
        $this->addSql('DROP INDEX IDX_45AD49DCA76ED395 ON product_comment');
        $this->addSql('ALTER TABLE product_comment ADD author VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP user_id');
    }
}
