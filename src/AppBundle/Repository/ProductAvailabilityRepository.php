<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductAvailability;

/**
 * ProductAvailabilityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductAvailabilityRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $product
     * @return null|\AppBundle\Entity\ProductAvailability
     */
    public function get($product)
    {
        $qb = $this->createQueryBuilder('pa');
        $qb->where($qb->expr()->eq('pa.product', ':productId'))
            ->setParameter(':productId', $product->getId());
        $validProductPromotions = $qb->getQuery()->getOneOrNullResult();
        return $validProductPromotions;
    }

    /**
     * @param Product $product
     * @param $availabilityCount
     */
    public function create($product, $availabilityCount)
    {
        $entity = new ProductAvailability();
        $entity->setProduct($product);
        $entity->setQuantity($availabilityCount);
        $em = $this->getEntityManager();
        $em->persist($entity);
    }

}
