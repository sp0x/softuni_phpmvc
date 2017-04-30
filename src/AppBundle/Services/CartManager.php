<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 24-Apr-17
 * Time: 9:35 PM
 */

namespace AppBundle\Services;


use AppBundle\Entity\CartItem;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductAvailability;
use AppBundle\Repository\CartItemRepository;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductAvailabilityRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\PromotionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CartManager
{
    /**
     * @var ProductRepository
     */
    protected $products;
    protected $promotions;
    protected $tokens;
    protected $categories;
    /**
     * @var ProductAvailabilityRepository
     */
    protected $availabilities;
    /**
     * @var CartItemRepository
     */
    protected $cartItems;


    public function __construct(ProductRepository $repo, PromotionRepository $promotionRepo, CategoryRepository $categories,
                                ProductAvailabilityRepository $availabilityRepo, CartItemRepository $cartRepo,
                                TokenStorage $tokenStore)
    {
        $this->products = $repo;
        $this->promotions = $promotionRepo;
        $this->tokens = $tokenStore;
        $this->categories = $categories;
        $this->availabilities = $availabilityRepo;
        $this->cartItems = $cartRepo;
    }

    public function addProductIdToCart($productId){
        if(!is_numeric($productId)) return false;
        $productId = (int)$productId;
        $product = $this->products->getById($productId);
        $user = $this->tokens->getToken()->getUser();
        $newCartItem = $this->cartItems->addIfNotExisting($product, $user);
        return true;
    }

    /**
     * @return CartItem[]|null
     */
    public function getMycart(){
        $user = $this->tokens->getToken()->getUser();
        $cartItems = $this->cartItems->getCurrentItemsByUser($user);
        return $cartItems;
    }




    public function getAvailability(Product $product){
        $availability = $this->availabilities->get($product);
        if($availability===null){
            return 0;
        }else{
            return $availability->getQuantity();
        }
    }

    public function newProductAvailability(Product $product, $availabilityCount){
        if(is_int($availabilityCount) || is_numeric($availabilityCount)){
            $this->availabilities->create($product, $availabilityCount);
        }
    }

    public function setAvailability(Product $product, ProductAvailability $availability){

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