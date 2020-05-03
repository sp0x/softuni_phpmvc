<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * User controller.
 *
 * @Route("user")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/all", name="user_list")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();
        return $this->render('user/list.html.twig', array(
            'users' => $users,
        ));
    }


    /**
     * @Route("/me", name="user_current")
     */
    public function currentAction(){
        $me = $this->getUser();
        return $this->render('user/showCurrent.html.twig', array(
            'user' => $me,
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/view/{id}", name="user_show")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(User $user)
    {
        return $this->render('user/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/createPosting")
     */
    public function createPostingAction()
    {
        return $this->render('AppBundle:User:create_posting.html.twig', array(
            // ...
        ));
    }

    /**
     * Displays a form to edit an existing category entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * @Route("/ban/{id}", name="user_ban")
     * @Security("has_role('ROLE_ADMIN')")
     *
     */
    public function userBanAction(User $user){
        $em = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepo */
        $userRepo = $em->getRepository(User::class);
        $userRepo->ban($user);
        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/unban/{id}", name="user_unban")
     * @Security("has_role('ROLE_ADMIN')")
     *
     */
    public function userUnbanAction(User $user){
        $em = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepo */
        $userRepo = $em->getRepository(User::class);
        $userRepo->unban($user);
        return $this->redirectToRoute('user_list');
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("DELETE")
     * @param Request $request
     * @param User $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, User $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('user_list');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
