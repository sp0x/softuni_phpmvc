<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 24-Apr-17
 * Time: 9:35 PM
 */

namespace AppBundle\Services;


use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductsManager
{
    /**
     * @var ProductRepository
     */
    protected $products;

    public function __construct(ProductRepository $repo)
    {
        $this->products = $repo;
    }

    /**
     * @return array
     */
    public function getAvailableInAllCategories(){
        $availables = $this->products->getAvailableProducts();
        return $availables;
    }

}