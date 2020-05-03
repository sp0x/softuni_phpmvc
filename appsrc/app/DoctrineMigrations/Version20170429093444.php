<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170429093444 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE host_ban (id INT AUTO_INCREMENT NOT NULL, host VARCHAR(255) NOT NULL, created DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, cost NUMERIC(2, 0) NOT NULL, is_available TINYINT(1) NOT NULL, created_on DATETIME NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_availability (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_comment (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, author VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_on DATETIME NOT NULL, INDEX IDX_45AD49DC4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_comment ADD CONSTRAINT FK_45AD49DC4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, ADD is_banned TINYINT(1) NOT NULL, ADD cash NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE upload_date upload_date DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_comment DROP FOREIGN KEY FK_45AD49DC4584665A');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE host_ban');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_availability');
        $this->addSql('DROP TABLE product_comment');
        $this->addSql('DROP INDEX UNIQ_8D93D64957698A6A ON user');
        $this->addSql('ALTER TABLE user DROP role, DROP is_banned, DROP cash');
        $this->addSql('ALTER TABLE video CHANGE upload_date upload_date DATETIME NOT NULL');
    }
}
