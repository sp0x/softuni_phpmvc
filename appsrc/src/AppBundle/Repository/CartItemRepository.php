<?php

namespace AppBundle\Repository;
use \AppBundle\Entity\CartItem;
use \AppBundle\Entity\Product;
use \AppBundle\Entity\User;

/**
 * CartItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CartItemRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param Product $product
     * @param User $user
     * @param int $quantity
     * @return CartItem
     */
    public function addIfNotExisting(Product $product, User $user, $quantity = 1){
        $citem = new CartItem();
        $citem->setQuantity($quantity);
        $citem->setProduct($product);
        $citem->setIsAvailable(true);
        $citem->setStatus('NONE');
        $citem->setUser($user);
        $em = $this->getEntityManager();
        $em->persist($citem);
        $em->flush();
        return $citem;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getCurrentItemsByUser(User $user){
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->eq('c.isAvailable', ':availability'))
            ->andwhere($qb->expr()->eq('c.user', ':user'))
            ->andWhere($qb->expr()->eq('c.status', ':status'))
            ->setParameter(':availability', true)
            ->setParameter(':status', 'NONE')
            ->setParameter(':user', $user);
        $item = $qb->getQuery()->getResult();
        return $item;
    }

    public function removeByProduct(Product $product, User $user){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $q=$qb->update(CartItem::class, 'c')
            ->set('c.isAvailable', $qb->expr()->literal(0))
            ->where($qb->expr()->eq('c.user', ':user'))
            ->andWhere($qb->expr()->eq('c.status', ':status'))
            ->andWhere($qb->expr()->eq('c.product', ':product'))
            ->setParameter(':status', 'NONE')
            ->setParameter(':user', $user)
            ->setParameter(':product', $product)
            ->getQuery();
        $item = $q->getSingleResult();
        return $item;
    }

    /**
     * @param CartItem $cartItem
     * @param $status
     * @param bool $deactivate
     * @return mixed
     */
    public function updateStatus(CartItem $cartItem, $status, $deactivate = true)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $activeField = $deactivate ? 0 : 1;
        $q=$qb->update(CartItem::class, 'c')
            ->set('c.isAvailable', $qb->expr()->literal($activeField))
            ->set('c.status', $qb->expr()->literal($status))
            ->where($qb->expr()->eq('c.id', ':cartId'))
            ->setParameter(':cartId', $cartItem->getId())
            ->getQuery();
        $item = $q->getSingleResult();
        return $item;
    }

    public function getNonCheckedout(Product $product, User $user){
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->eq('c.isAvailable', ':availability'))
            ->andwhere($qb->expr()->eq('c.user', ':user'))
            ->andWhere($qb->expr()->eq('c.status', ':status'))
            ->andWhere($qb->expr()->eq('c.product', ':product'))
            ->setParameter(':availability', true)
            ->setParameter(':status', 'NONE')
            ->setParameter(':user', $user)
            ->setParameter(':product', $product);
        $item = $qb->getQuery()->getOneOrNullResult();
        return $item;
    }

    public function itemExists(Product $product, User $user){
        return $this->getNonCheckedout($product, $user)!=null;
    }



}