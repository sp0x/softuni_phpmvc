<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class CartController
 * @Route("cart",  name="cart")
 * @package AppBundle\Controller
 */
class CartController extends Controller
{
    /**
     * @Route("/add", name="cart_add")
     */
    public function addProductAction()
    {
        return $this->render('AppBundle:CartController:add_product.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/remove", name="cart_remove")
     */
    public function removeProductAction()
    {
        return $this->render('AppBundle:CartController:remove_product.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/modify", name="cart_modify")
     */
    public function modifyProductAction()
    {
        return $this->render('AppBundle:CartController:modify_product.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/checkout", name="cart_checkout")
     */
    public function checkoutAction()
    {
        return $this->render('AppBundle:CartController:checkout.html.twig', array(
            // ...
        ));
    }

}
