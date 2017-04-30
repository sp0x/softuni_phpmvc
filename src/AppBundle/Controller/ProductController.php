<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductAvailability;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Intl\Intl;

/**
 * Product controller.
 *
 * @Route("products", name="products")
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     *
     * @Route("/", name="product_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')->findAll();
        return $this->render('product/index.html.twig', array(
            'products' => $products,
        ));
    }


    public function addCommentAction(Request $request){

    }
    /**
     * Creates a new product entity.
     *
     * @Route("/new", name="product_new")
     * @Security("has_role('ROLE_EDITOR')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('AppBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedOn(new DateTime());
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

                $this->get('session')->getFlashBag()->add('success', 'Product was created successfully!');

                $initialQuantity = $product->getInitialQuantity();
                if($initialQuantity !=null){
                    $this->get('app.productsmanager')->newProductAvailability($product, $initialQuantity);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute('product_show', array('id' => $product->getId()));
            }
        }

        return $this->render('product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/{id}", name="product_show")
     * @Method("GET")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $currency = Intl::getCurrencyBundle()->getCurrencySymbol('EUR');
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        // Example with parameter injected into translation "user.profile"
        $categoryUrl = $this->get('router')->generate('category_products', array('id' => $product->getCategory()->getId()));
        $pm = $this->get('app.productsmanager');

        $breadcrumbs->addItem($product->getCategory()->getName(), $categoryUrl);
        $breadcrumbs->addItem($product->getName());

        return $this->render('product/show.html.twig', array(
            'availability' => $pm->getAvailability($product),
            'product' => $product,
            'currency' => $currency,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Product $product
     * @Route("/promote/{id}", name="product_promote")
     * @Security("has_role('ROLE_EDITOR')")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function productPromoteAction(Product $product){
        return $this->redirectToRoute("promotion_new", [
            'product_id' => $product->getId()
        ]);
    }


    /**
     * @param Request $request
     * @Security("has_role('ROLE_EDITOR')")
     * @Route("/quantity/set/{id}", name="product_set_quantity")
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function setQuantityAction(Request $request, Product $product){
        $availability = new ProductAvailability();
        $em = $this->getDoctrine()->getManager();
        $storedAvailability = $em->getRepository(ProductAvailability::class)->findOneBy(['product'=> $product]);

        $availabilityForm = $this->createForm('AppBundle\Form\ProductAvailabilityType', $availability);
        $availabilityForm->handleRequest($request);

        $pm = $this->get('app.productsmanager');
        if ($availabilityForm->isSubmitted() && $availabilityForm->isValid()) {
            $storedAvailability->setQuantity($availability->getQuantity());
            $em->flush();

            $this->addFlash('success',  'Product was updated successfully!');
            return $this->redirectToRoute('product_index');
        }else if(!$availabilityForm->isSubmitted()){
            $availabilityForm->get('quantity')->setData($pm->getAvailability($product));

        }

        return $this->render('product/availability.html.twig', array(
            'product' => $product,
            'edit_form' => $availabilityForm->createView(),
        ));
    }


    /**
     * @param Request $request
     * @Security("has_role('ROLE_EDITOR')")
     * @Route("/quantity/define/{id}", name="product_define_quantity")
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function defineQuantityAction(Request $request, Product $product){
        $availability = new ProductAvailability();
        $availabilityForm = $this->createForm('AppBundle\Form\ProductAvailabilityType', $availability);
        $availabilityForm->handleRequest($request);

        $pm = $this->get('app.productsmanager');
        if ($availabilityForm->isSubmitted() && $availabilityForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $availability->setProduct($product);
            $em->persist($availability);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }else if(!$availabilityForm->isSubmitted()){
            $availabilityForm->get('formProductId')->setData($product->getId());
        }

        return $this->render('product/availability.html.twig', array(
            'availability' => $availability,
            'edit_form' => $availabilityForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Security("has_role('ROLE_EDITOR')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('AppBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $product->setDateUpdated(new \DateTime());
            if ($product->getImageForm() instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $file = $product->getImageForm();

                $filename = md5($product->getName() . '' . $product->getDateUpdated()->format('Y-m-d H:i:s'));
                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success',  'Product was updated successfully!');
            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Security("has_role('ROLE_EDITOR')")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
