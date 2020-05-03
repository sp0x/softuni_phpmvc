<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 29-Apr-17
 * Time: 10:04 AM
 */

namespace AppBundle\Utils;


use AppBundle\Entity\HostBan;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class VisitorValidator
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * HostValidator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Checks if there is a ban for a given user, by his username.
     * @param $userName
     * @return bool
     */
    public function isUserAllowed($userName){
        $userEntityName = $this->em->getClassMetadata(User::class)->getName();
        $qb = $this->em->createQueryBuilder()
            ->select('u')
            ->from($userEntityName, 'u', null);
        $qb->where($qb->expr()->orX(
            $qb->expr()->eq('u.username', ':username'),
            $qb->expr()->eq('u.is_banned', ':banned')
        ))
            ->setParameter(':username', $userName)
            ->setParameter(':banned', true);
        $userBan = $qb->getQuery()->getOneOrNullResult();
        $b = $userBan == null;
        return $b;
    }

    /**
     * Checks if there is a ban for a given host
     * @param $host
     * @return bool
     */
    public function isHostAllowed($host){
        $hostbanName = $this->em->getClassMetadata(Hostban::class)->getName();
        $qb = $this->em->createQueryBuilder()
            ->select('h')
            ->from($hostbanName, 'h', null);
        $qb->where($qb->expr()->orX(
                $qb->expr()->eq('h.host', ':host'),
                $qb->expr()->eq('h.is_active', ':active')
            ))
            ->setParameter(':host', $host)
            ->setParameter(':active', true);
        $hostban = $qb->getQuery()->getOneOrNullResult();
        $b = $hostban == null;
        return $b;
    }
}