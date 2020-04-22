<?php

namespace Application\Migrations;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170429145438 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        // this up() migration is auto-generated, please modify it to your needs
        $crypto = $this->container->get('security.password_encoder');
        $doctrine = $this->container->get('doctrine');

        $newAdmin = new User();
        $newAdmin->setUsername('admin');
        $newAdmin->setEmail('admin@userx.com');
        $newAdmin->setPasswordRaw('alongpass');
        $newAdmin->setIsBanned(false);
        $newAdmin->setIsActive(true);
        //$newAdmin->setCreatedOn(new \DateTime());
        $newAdmin->setCash(1000);
        $newAdmin->setRole("ROLE_ADMIN");
        $newAdmin->setPassword($crypto->encodePassword($newAdmin, $newAdmin->getPasswordRaw()));

        $newMod = new User();
        $newMod->setUsername('moderator');
        $newMod->setEmail('moderator@userx.com');
        $newMod->setPasswordRaw('modulus');
        $newMod->setIsBanned(false);
        $newMod->setIsActive(true);
        //$newMod->setCreatedOn(new \DateTime());
        $newMod->setCash(1000);
        $newMod->setRole("ROLE_EDITOR");
        $newMod->setPassword($crypto->encodePassword($newMod, $newMod->getPasswordRaw()));

        $manager = $doctrine->getManager();
        $manager->persist($newAdmin);
        $manager->persist($newMod);
        $manager->flush();
        //$this->addSql('');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        // this up() migration is auto-generated, please modify it to your needs
        $doctrine = $this->container->get('doctrine');
        $manager = $doctrine->getManager();

//        $userRepo = $manager->getRepository(UserRepository::class);
//        $userRepo->removeByUsername("admin");
//        $userRepo = $manager->getRepository(UserRepository::class);
//        $userRepo->removeByUsername("moderator");
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
