<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Class CartController
 * @Route("cart",  name="cart")
 * @package AppBundle\Controller
 */
class CartController extends Controller
{

    /**
     * @Route("/list", name="list_cart")
     */
    public function viewCartAction()
    {
        $cm = $this->get('app.cartmanager');
        $pm = $this->get('app.productsmanager');
        $cartResult = $cm->getMyCartWithPromotions($pm);
        $cart = @$cartResult['cart'];
        $total = @$cartResult['total'];

        $currency = Intl::getCurrencyBundle()->getCurrencySymbol('EUR');

        return $this->render('AppBundle:Cart:list.html.twig', array(
            'cart' => $cart,
            'total' => $total,
            'currency' => $currency
        ));
    }

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
        return $this->redirectToRoute('list_cart');
    }

    /**
     * @Route("/checkout", name="cart_checkout")
     */
    public function checkoutAction(Request $request)
    {
        $cm = $this->get('app.cartmanager');
        $pm = $this->get('app.productsmanager');
        $buff = $request->get('buff');

        //Calculate the total with the best promotions
        $cartResult = $cm->getMyCartWithPromotions($pm);
        $cart = @$cartResult['cart'];
        $total = @$cartResult['total'];
        //Apply the quantities that we recieved
        $cart = $cm->applyQuantities($cart, $buff);

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
        return $this->render('AppBundle:Cart:checkout.html.twig', array(
            'items' => $cart,
            'success' => $success
        ));
    }

}
