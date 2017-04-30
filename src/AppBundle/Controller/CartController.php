<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    public function addProductAction(Request $request)
    {
        $cartman = $this->get('app.cartmanager');
        $success = $cartman->addProductIdToCart($request->get('id'));
        $message = "Something went wrong, please try again later!";
        if($success){
            $message = "Your item was added";
        }
        return new JsonResponse(array('success' => $success, 'message' => $message));
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
