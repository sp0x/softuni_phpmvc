<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
    public function removeProductAction(Request $request)
    {
        $cartman = $this->get('app.cartmanager');
        $success = $cartman->removeProductIdFromCart($request->get('id'));
        $message = "Something went wrong, please try again later!";
        if($success){
            $message = "Your item was added";
        }
        return $this->redirectToRoute('mycart');
    }

    /**
     * @Route("/checkout", name="cart_checkout")
     */
    public function checkoutAction()
    {
        $cm = $this->get('app.cartmanager');
        $pm = $this->get('app.productsmanager');
        //Calculate the total with the best promotions
        $cartResult = $cm->getMyCartWithPromotions($pm);
        $cart = @$cartResult['cart'];
        $total = @$cartResult['total'];
        $success = false;
        /** @var User $user */
        $user = $this->getUser();
        if($cart!=null){
            $success = $cm->checkoutCartItems($cart, $total, $pm);
            if($success){
                $em = $this->getDoctrine()->getManager();
                $leftover = $user->getCash() - $total;
                $user->setCash($leftover);
                $em->persist($user);
                $em->flush();
            }
        }
        return $this->render('AppBundle:CartController:checkout.html.twig', array(
            'items' => $cart,
            'success' => $success
        ));
    }

}
