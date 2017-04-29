<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 24-Apr-17
 * Time: 9:35 PM
 */

namespace AppBundle\Services;


use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Repository\CategoryRepository;
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
    protected $categories;

    public function __construct(ProductRepository $repo, PromotionRepository $promotionRepo, CategoryRepository $categories, TokenStorage $tokenStore)
    {
        $this->products = $repo;
        $this->promotions = $promotionRepo;
        $this->tokens = $tokenStore;
        $this->categories = $categories;
    }

    /**
     * @return Category[]
     */
    public function getAvailableInAllCategories(){
        $availables = $this->categories->getAll();
        return $availables;
    }

    /**
     * @param Category $category
     * @return array
     */
    public function getAvailableInCategory(Category $category){
        return $this->products->getByCategory($category->getId());
    }

    public function putOnPromotion(Product $product){

    }

    public function applyAvailablePromotions(Product &$product){
        $productPromotions = $this->promotions->getProductPromotions($product);

    }

}