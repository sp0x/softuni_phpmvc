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
use AppBundle\Entity\User;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductAvailabilityRepository;
use AppBundle\Repository\ProductCommentRepository;
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
    /**
     * @var PromotionRepository
     */
    protected $promotions;
    /**
     * @var TokenStorage
     */
    protected $tokens;
    /**
     * @var CategoryRepository
     */
    protected $categories;
    /**
     * @var ProductAvailabilityRepository
     */
    protected $availabilities;
    /**
     * @var ProductCommentRepository
     */
    protected $comments;
    /**
     * @var Promotion[]
     */
    protected $loadedPromotions;

    public function __construct(ProductRepository $repo, PromotionRepository $promotionRepo, CategoryRepository $categories,
                                ProductAvailabilityRepository $availabilityRepo, ProductCommentRepository $commentsRepo,
                                TokenStorage $tokenStore)
    {
        $this->products = $repo;
        $this->promotions = $promotionRepo;
        $this->tokens = $tokenStore;
        $this->categories = $categories;
        $this->availabilities = $availabilityRepo;
        $this->comments = $commentsRepo;
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

    public function addComment(Product $product, $comment, User $user){
        $comment = $this->comments->create($product, $comment, $user);
        return true;
    }



    public function putOnPromotion(Product $product){

    }

    /**
     * Applies the best available promotion to the product
     * @param Product $product
     * @param null $discount
     * @internal param int|null $amount
     */
    public function applyAvailablePromotions(Product &$product, &$discount=null){
        $bestPromotion = $this->getBestPromotion($product);
        if($bestPromotion!=null){
            $product->setPromotion($bestPromotion);
            $discount= $bestPromotion->getDiscount();
        }
    }

    /**
     * checks if the promotions are loaded
     */
    private function checkPromotions(){
        if($this->loadedPromotions!=null) return;
        $this->loadedPromotions = $this->promotions->getAvailablePromotions();
    }

    /**
     * @param Product $product
     * @return array [ product => Product, discount => Discount]
     */
    public function applyStaticDiscounts(Product $product){
        $this->checkPromotions();
        $productQualified = $this->getQualifiedPromotions($product, $this->loadedPromotions);
        $maxDiscount = -1;
        $biggestPromotion = null;
        for ($i = 0; $i < count($productQualified); $i++) {
            /** @var Promotion $p */
            $p = $productQualified[$i];
            if ($p->getDiscount() > $maxDiscount) {
                $maxDiscount = $p->getDiscount();
                $biggestPromotion = $p;
            }
        }
        $discount = 0;
        if($biggestPromotion!=null){
            $product->setPromotion($biggestPromotion);
            $discount= $biggestPromotion->getDiscount();
        }
        return [
            'product' => $product,
            'discount' => $discount
        ];
    }

    /**
     * @param Product $product
     * @return Promotion|null
     */
    protected function getBestPromotion(Product $product)    {
        $productPromotion = $this->promotions->getProductPromotion($product);
        $allSpecialPromotions = $this->promotions->getSpecialPromotions();
        $qualifiedSpecialPromotions = $this->getQualifiedCriterionPromotions($allSpecialPromotions);

        if($productPromotion==null) $productPromotion = [];
        else{
            $productPromotion = [$productPromotion];
        }
        $allPromotionsClassified = array_merge($productPromotion, $qualifiedSpecialPromotions);
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

    /**
     * Gets all promotions that qualify for the given product or match a criteria
     * @param Product $product
     * @param Promotion[] $promotions
     * @return Promotion[]
     */
    public function getQualifiedPromotions(Product $product, $promotions){
        $qualifiedPromos = [];
        $user = $this->tokens->getToken()->getUser();
        foreach($promotions as $promotion){
            $valid = false;
            $promoProduct = $promotion->getProduct();
            if($promoProduct !==null && $promoProduct->getId() === $product->getId()){
                $valid = true;
            }else if($promotion->getIsGeneral()){
                $valid = true;
            }else if($promotion->getCategory()!=null && $promotion->getCategory()->getId() === $product->getCategory()->getId()){
                $valid = true;
            }else if($promotion->getCriteria()!==null){
                $criterion = $promotion->getCriteria();
                $valid = $this->promotionMatchesCriteria($criterion, $user);
            }

            if($valid){
                $qualifiedPromos[] = $promotion;
            }
        }
        return $qualifiedPromos;
    }

    /**
     * Gets all the promotions from the array which qualify for the current user.
     * Use this to filter promotions with criterias
     * @param $allSpecialPromotions
     * @return array
     */
    protected function getQualifiedCriterionPromotions($allSpecialPromotions)
    {
        $qualifiedSpecialPomotions = [];
        $user = $this->tokens->getToken()->getUser();
        //Qualify the normal promotions and the user promotions
        /** @var Promotion $specialPromotion */
        foreach ($allSpecialPromotions as $specialPromotion) {
            $criterion = $specialPromotion->getCriteria();
            $valid = $this->promotionMatchesCriteria($criterion, $user);
            if ($valid) {
                $qualifiedSpecialPomotions[] = $specialPromotion;
            }
        }
        return $qualifiedSpecialPomotions;
    }

    /**
     * @param $criterion
     * @param $user
     * @return bool
     */
    protected function promotionMatchesCriteria($criterion, User $user)
    {
        $role = $user->getRole();
        $valid = false;
        if ($criterion == "USER_IS_ADMIN") {
            if ($role == "ROLE_ADMIN") {
                $valid = true;
                return $valid;
            }
            return $valid;
        } else if ($criterion == "USER_REGISTERED_1D") {
            $registeredOn = $user->getCreatedOn();
            $now = new \DateTime();
            $diff = $now->diff($registeredOn);
            $days = $diff->format('%R%a');
            $days = ltrim($days, '+');
            $days = (int)$days;
            if ($days > 1) {
                $valid = true;
                return $valid;
            }
            return $valid;
        } else if ($criterion == "USER_CREDIT_100") {
            $userCredit = $user->getCash();
            if ($userCredit > 100) {
                $valid = true;
                return $valid;
            }
            return $valid;
        }return $valid;
    }

}