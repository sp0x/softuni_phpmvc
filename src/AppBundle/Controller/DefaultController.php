<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);

        //get products from a category, or list all products in category groups
        //add to product cart
        /**
         * cart:
         *  - items (item: quantity, product, user, status\
         *
         * sale:
         *  - (purchase_id, quantity, product, user, created_on)
         *
         * controllers:
         * cart:
         *  - checkout
         *
         * user:
         *  - create a sale for a purchased product
         *  - view cart
         * admin:
         *  - same as morderator
         *  - product editing
         *  - category editing
         *  - user editing
         *  - cart editing
         * moderate:
         *  - add or delete category
         *  - add or delete product
         *  - change a product's category
         *  - change the available quantity
         *  - reoder products
         *
         */
    }
}
