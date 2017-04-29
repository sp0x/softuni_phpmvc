<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{

    /**
     * @Route("/login", name="user_login")
     * @Template()
     * @return array
     */
    public function loginAction(){
        //$this->render , but im using templates
        $string = "some text";
        $slugger = $this->get('app.slugger');
        $slugger->slugify($string);

        $auth_utils = $this->get('security.authentication_utils');
        $error = $auth_utils->getLastAuthenticationError();
        $last_user = $auth_utils->getLastUsername();

        return [
            'last_username' => $last_user,
            'error' => $error
        ];
    }




    /**
     * @Route("/register", name="user_register")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function registerAction(Request $request){
        /*
         * return $this->render('registration/register.html.twig', [ 'form' => $form->createView() ])
         */
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //register the user
            /** @var User $user */
            $user = $form->getData();
            $crypto = $this->get('security.password_encoder');
            $user->setPassword($crypto->encodePassword($user, $user->getPasswordRaw()));

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('user_login');
        }

        return [
            'form' => $form->createView()
        ];
    }
}
