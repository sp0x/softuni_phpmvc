<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN= 'ROLE_ADMIN';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     *
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @Assert\Length(min="4")
     * @var
     */
    private $password_raw;

    /**
     * @var CartItem[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CartItem", mappedBy="user")
     */
    private $cartItems;


    /**
     * @var ProductComment[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductComment", mappedBy="author")
     */
    private $comments;

    /**
     * @var Sale[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Sale", mappedBy="user")
     */
    private $sales;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     *
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_banned" , type="boolean")
     * @var boolean
     */
    private $isBanned;

    /**
     * @ORM\Column(name="cash", type="decimal", precision=10, scale=2)
     * @var
     */
    private $cash;

    /**
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     * @var
     */
    private $createdOn;

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }



    public function __construct(){
        $this->isActive = true;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isIsBanned()
    {
        return $this->isBanned;
    }

    /**
     * @param bool $isBanned
     */
    public function setIsBanned($isBanned)
    {
        $this->isBanned = $isBanned;
    }

    /**
     * @return mixed
     */
    public function getCash()
    {
        return $this->cash;
    }

    /**
     * @param mixed $cash
     */
    public function setCash($cash)
    {
        $this->cash = $cash;
    }



    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }


    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }



    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password raw
     *
     * @param string $password
     *
     * @return User
     */
    public function setPasswordRaw($password)
    {
        $this->password_raw = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPasswordRaw()
    {
        return $this->password_raw;
    }


    /**
     * Returns the roles granted to the user.
     *
     */
    public function getRole()
    {
        return $this->role;
    }

    public function isEditor(){
        return $this->getRole()==self::ROLE_EDITOR;
    }

    public function isAdmin(){
        return $this->getRole()==self::ROLE_ADMIN;
    }

    public function getDefaultRole(){
        return self::ROLE_USER;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return explode(',', $this->getRole());
    }

    public function getAllRoles(){
        return [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_EDITOR];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }


    /**
     * Get isBanned
     *
     * @return boolean
     */
    public function getIsBanned()
    {
        return $this->isBanned;
    }

    /**
     * Add cartItem
     *
     * @param \AppBundle\Entity\CartItem $cartItem
     *
     * @return User
     */
    public function addCartItem(\AppBundle\Entity\CartItem $cartItem)
    {
        $this->cartItems[] = $cartItem;

        return $this;
    }

    /**
     * Remove cartItem
     *
     * @param \AppBundle\Entity\CartItem $cartItem
     */
    public function removeCartItem(\AppBundle\Entity\CartItem $cartItem)
    {
        $this->cartItems->removeElement($cartItem);
    }

    /**
     * Get cartItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartItems()
    {
        return $this->cartItems;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\ProductComment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\ProductComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\ProductComment $comment
     */
    public function removeComment(\AppBundle\Entity\ProductComment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add sale
     *
     * @param \AppBundle\Entity\Sale $sale
     *
     * @return User
     */
    public function addSale(\AppBundle\Entity\Sale $sale)
    {
        $this->sales[] = $sale;

        return $this;
    }

    /**
     * Remove sale
     *
     * @param \AppBundle\Entity\Sale $sale
     */
    public function removeSale(\AppBundle\Entity\Sale $sale)
    {
        $this->sales->removeElement($sale);
    }

    /**
     * Get sales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSales()
    {
        return $this->sales;
    }
}
