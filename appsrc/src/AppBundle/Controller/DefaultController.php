<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $productsManager = $this->get('app.productsmanager');
        //$products = $productsManager->getAvailableInAllCategories();
        $categories = $productsManager->getAvailableInAllCategories();
        $currency = Intl::getCurrencyBundle()->getCurrencySymbol('EUR');

        return $this->render('@App/Default/index.html.twig', [
            'currency' => $currency,
            'categories' => $categories,
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);

    }
}
