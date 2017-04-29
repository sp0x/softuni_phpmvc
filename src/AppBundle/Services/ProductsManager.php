<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 24-Apr-17
 * Time: 9:35 PM
 */

namespace AppBundle\Services;


use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\PromotionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ProductsManager
{
    /**
     * @var ProductRepository
     */
    protected $products;
    protected $promotions;
    protected $tokens;

    public function __construct(ProductRepository $repo, PromotionRepository $promotionRepo, TokenStorage $tokenStore)
    {
        $this->products = $repo;
        $this->promotions = $promotionRepo;
        $this->tokens = $tokenStore;
    }

    /**
     * @return array
     */
    public function getAvailableInAllCategories(){
        $availables = $this->products->getAvailableProducts();
        return $availables;
    }

    public function putOnPromotion(Product $product){

    }

    public function applyAvailablePromotions(Product &$product){
        $productPromotions = $this->promotions->getProductPromotions($product);

    }

}