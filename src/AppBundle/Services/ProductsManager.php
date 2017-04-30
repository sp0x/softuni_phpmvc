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
use AppBundle\Entity\ProductAvailability;
use AppBundle\Entity\Promotion;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductAvailabilityRepository;
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
    /**
     * @var ProductAvailabilityRepository
     */
    protected $availabilities;

    public function __construct(ProductRepository $repo, PromotionRepository $promotionRepo, CategoryRepository $categories,
                                ProductAvailabilityRepository $availabilityRepo, TokenStorage $tokenStore)
    {
        $this->products = $repo;
        $this->promotions = $promotionRepo;
        $this->tokens = $tokenStore;
        $this->categories = $categories;
        $this->availabilities = $availabilityRepo;
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

    public function subtractAvailability(Product $product, $intToSubtract){
        if(is_int($intToSubtract) || is_numeric($intToSubtract)){
            $availability = $this->availabilities->get($product);
            if($availability===null){
                return 0;
            }else{
                $newAvailability = $availability->getQuantity() - $intToSubtract;
                $this->availabilities->set($product, $newAvailability);
                return $newAvailability;
            }
        }
        return false;
    }

    public function setAvailability(Product $product, ProductAvailability $availability){
        $this->availabilities->set($product, $availability->getQuantity());
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

    /**
     * Applies the best available promotion to the product
     * @param Product $product
     */
    public function applyAvailablePromotions(Product &$product){
        $bestPromotion = $this->getBestPromotion($product);
        if($bestPromotion!=null){
            $product->setPromotion($bestPromotion);
        }
    }

    /**
     * @param Product $product
     * @return Promotion|null
     */
    protected function getBestPromotion(Product $product)
    {
        $productPromotion = $this->promotions->getProductPromotion($product);
        $allSpecialPromotions = $this->promotions->getSpecialPromotions();
        $qualifiedSpecialPomotions = [];
        $user = $this->tokens->getToken()->getUser();
        //Qualify the normal promotions and the user promotions
        foreach ($allSpecialPromotions as $specialPromotion) {
            $criterion = $specialPromotion->getCriteria();
            $role = $user->getRole();
            $valid = false;
            if ($criterion == "USER_IS_ADMIN") {
                if ($role == "ROLE_ADMIN") {
                    $valid = true;
                }
            } else if ($criterion == "USER_REGISTERED_1D") {
                $registeredOn = $user->getCreatedOn();
                $now = new \DateTime();
                $diff = $now->diff($registeredOn);
                $days = $diff->format('%R%a');
                $days = ltrim($days, '+');
                $days = (int)$days;
                if ($days > 1) {
                    $valid = true;
                }
            } else if ($criterion == "USER_CREDIT_100") {
                $userCredit = $user->getCash();
                if ($userCredit > 100) {
                    $valid = true;
                }
            }
            if ($valid) {
                $qualifiedSpecialPomotions[] = $specialPromotion;
            }
        }
        if($productPromotion==null) $productPromotion = [];
        else{
            $productPromotion = [$productPromotion];
        }
        $allPromotionsClassified = array_merge($productPromotion, $qualifiedSpecialPomotions);
        $maxDiscount = -1;
        $biggestPromotion = null;
        for ($i = 0; $i < count($allPromotionsClassified); $i++) {
            /** @var Promotion $p */
            $p = $allPromotionsClassified[$i];
            if ($p->getDiscount() > $maxDiscount) {
                $maxDiscount = $p->getDiscount();
                $biggestPromotion = $p;
            }
        }

        return $biggestPromotion;
    }

}