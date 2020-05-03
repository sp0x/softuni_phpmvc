<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProductAvailability
 *
 * @ORM\Table(name="product_availability")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductAvailabilityRepository")
 */
class ProductAvailability
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Product
     * @ORM\OneToOne(targetEntity="Product", inversedBy="availability")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    protected $formProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\GreaterThan(-1)
     *
     */
    private $quantity;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFormProductId()
    {
        return $this->formProductId;
    }

    /**
     * @param mixed $formProductId
     */
    public function setFormProductId($formProductId)
    {
        $this->formProductId = $formProductId;
    }


    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return ProductAvailability
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct(){
        return $this->product;
    }

}
