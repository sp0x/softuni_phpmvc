<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sale;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sale controller.
 *
 * @Route("sales")
 */
class SaleController extends Controller
{
    /**
     * Lists all sale entities.
     *
     * @Route("/", name="sales_index")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sales = $em->getRepository('AppBundle:Sale')->findAll();

        return $this->render('sale/index.html.twig', array(
            'sales' => $sales,
        ));
    }

    /**
     * Finds and displays a sale entity.
     *
     * @Route("/{id}", name="sales_show")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showAction(Sale $sale)
    {
        return $this->render('sale/show.html.twig', array(
            'sale' => $sale,
        ));
    }

    /**
     * Finds and displays a sale entity.
     *
     * @Route("/delete/{id}", name="sales_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Sale $sale)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($sale);
        $em->flush();
        return $this->redirectToRoute('sales_index');

    }
}
