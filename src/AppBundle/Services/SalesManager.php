<?php

namespace AppBundle\Services;


use AppBundle\Entity\CartItem;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductAvailability;
use AppBundle\Entity\Sale;
use AppBundle\Entity\User;
use AppBundle\Repository\CartItemRepository;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductAvailabilityRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\PromotionRepository;
use AppBundle\Repository\SaleRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SalesManager
{
    protected $sales;


    public function __construct(SaleRepository $salesRepo)
    {
        $this->sales = $salesRepo;
    }


    public function findByUser(User $user){
        return $this->sales->getByUser($user);
    }


}