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
use AppBundle\Entity\Sale;
use AppBundle\Repository\CartItemRepository;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductAvailabilityRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\PromotionRepository;
use AppBundle\Repository\SaleRepository;
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
                                SaleRepository $salesRepo,
                                TokenStorage $tokenStore)
    {
        $this->products = $repo;
        $this->promotions = $promotionRepo;
        $this->tokens = $tokenStore;
        $this->categories = $categories;
        $this->availabilities = $availabilityRepo;
        $this->cartItems = $cartRepo;
        $this->sales =  $salesRepo;
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
     * @param $productId
     * @return bool
     */
    public function removeProductIdFromCart($productId){
        if(!is_numeric($productId)) return false;
        $productId = (int)$productId;
        $product = $this->products->getById($productId);
        $user = $this->tokens->getToken()->getUser();
        $this->cartItems->removeByProduct($product, $user);
        return true;
    }

    /**
     * @param CartItem[] $cartItems
     * @param ProductsManager $productsManager
     * @param null $purchaseId
     * @return bool
     */
    public function checkoutCartItems($cartItems, ProductsManager $productsManager, &$purchaseId=null){
        if($cartItems==null || count($cartItems) ==0) return false;
        //checkout cart
        $user = $this->tokens->getToken()->getUser();
        $purchaseGuid = $this->GUIDv4();
        foreach($cartItems as $cartItem){
            $cartItem->setUser($user);
            if($cartItem->getQuantity()===null){
                $cartItem->setQuantity(0);
            }
            $sale = $this->checkoutCart($cartItem, $purchaseGuid, $productsManager);

            //If the sale went alright, mark our checkout item as inactive and checked out
            if($sale!=null){
                $this->cartItems->updateStatus($cartItem, "CHECKED_OUT", true);
            }
        }
        return true;
    }

    /**
     * @param CartItem $cartItem
     * @param string $guid The unique purchase id to provide to the sale
     * @param ProductsManager $productsManager
     * @return Sale
     */
    public function checkoutCart(CartItem $cartItem, $guid, ProductsManager $productsManager){
        $product = $cartItem->getProduct();
        $newSale = $this->sales->createByCartItem($cartItem, $guid);
        $productsManager->subtractAvailability($product, $cartItem->getQuantity());
        return $newSale;
    }

    /**
     * @return CartItem[]|null
     */
    public function getMycart(){
        $user = $this->tokens->getToken()->getUser();
        $cartItems = $this->cartItems->getCurrentItemsByUser($user);
        return $cartItems;
    }


    public function getMyCartWithPromotions(ProductsManager $pm){
        $cart = $this->getMycart();
        $total = 0;
        if($cart!=null){
            foreach($cart as $k => $cartItem){
                $product = $cartItem->getProduct();
                $pm->applyAvailablePromotions($product);
                $cartItem->setProduct($product);
                $total += $cartItem->getTotalPrice();
            }
        }
        return [
            'cart' => $cart,
            'total' => $total
        ];
    }


    function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
            substr($charid,  0,  8).$hyphen.
            substr($charid,  8,  4).$hyphen.
            substr($charid, 12,  4).$hyphen.
            substr($charid, 16,  4).$hyphen.
            substr($charid, 20, 12).
            $rbrace;
        return $guidv4;
    }

}