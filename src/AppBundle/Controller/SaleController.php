<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Sale;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/me", name="sales_user_sales")
     */
    public function userSalesActions(){
        $user = $this->getUser();
        $sales = $this->get('app.salesmanager')->findByUser($user);
        return $this->render('sale/userSales.html.twig', array(
            'sales' => $sales,
        ));
    }


    /**
     * @Route("/resell/{id}", name="sales_resell")
     */
    public function resellAction(Request $request, Sale $sale){
        $product = new Product();
        $form = $this->createForm('AppBundle\Form\ResellType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedOn(new \DateTime());
            /** @var UploadedFile $file */
            $file = $product->getImageForm();
            if(!$file){
                $form->get('image_form')->addError(new FormError('Image is required'));
            }else{
                $filename = md5($product->getName() . '' . $product->getCreatedOn()->format('Y-m-d H:i:s'));
                $path = $this->get('kernel')->getRootDir();
                $pathSuffix = '/../web/images/product/';
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $pathSuffix = "\\..\\web\\images\\product\\";
                } else {
                }
                $path .= $pathSuffix;
                $file->move(  $path,  $filename );
                $product->setImage($filename);
                $product->setIsAvailable(true);
                $product->setStatus(Product::STATUS_USED);
                $product->setOrder(999);

                $this->get('session')->getFlashBag()->add('success', 'Product was posted for sale successfully!');
                $this->get('app.productsmanager')->newProductAvailability($product, 1);


                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute('product_show', array('id' => $product->getId()));
            }
        }else if(!$form->isSubmitted()){
            $oldProduct = $sale->getProduct();
            $form->get('name')->setData($oldProduct->getName());
            $form->get('cost')->setData($oldProduct->getCost());
            $form->get('description')->setData($oldProduct->getDescription());
            $form->get('category')->setData($oldProduct->getCategory());
        }

        return $this->render('product/newResale.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
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
