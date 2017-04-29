<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 29-Apr-17
 * Time: 3:02 PM
 */

namespace AppBundle\Menu;


use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    /**
     * Builder constructor.
     */
    public function __construct()
    {
    }

    private function isLoggedIn(){
        $authChecker = $this->container->get('security.authorization_checker');
        //If the user is not logged on
        $isAnon = $authChecker->isGranted('IS_AUTHENTICATED_ANONYMOUSLY');
        $isRemembered = $authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $isFullyLogged = $authChecker->isGranted('IS_AUTHENTICATED_FULLY');
        //If the user is logged
        return($isRemembered || $isFullyLogged);
    }

    public function rightMenu(FactoryInterface $factory, array $options){
        $menu = $factory->createItem('root');
        //If the user is logged
        if($this->isLoggedIn()){

        }else{
            $currentRoute = $this->container->get('app.requesthelper')->getCurrentRoute();
            if($currentRoute=="user_login"){
                $menu->addChild('Register', array('route' => 'user_register'));
            }else{
                $menu->addChild('Login', array('route' => 'user_login'));
            }
        }
        return $menu;
    }
    public function leftMenu(FactoryInterface $factory, array $options){
        $menu = $factory->createItem('root');
        if($this->isLoggedIn()){
            $routes = $this->container->get('router')->getRouteCollection();
            $menu->addChild('Products', array('route' => $routes->get('products')));
        }
        return $menu;
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

//        $menu->addChild('Home', array('route' => 'homepage'));
//
//        // access services from the container!
//        $em = $this->container->get('doctrine')->getManager();
//        // findMostRecent and Blog are just imaginary examples
//        $blog = $em->getRepository('AppBundle:Blog')->findMostRecent();
//
//        $menu->addChild('Latest Blog Post', array(
//            'route' => 'blog_show',
//            'routeParameters' => array('id' => $blog->getId())
//        ));
//
//        // create another menu item
//        $menu->addChild('About Me', array('route' => 'about'));
//        // you can also add sub level's to your menu's as follows
//        $menu['About Me']->addChild('Edit profile', array('route' => 'edit_profile'));
//
//        // ... add more children

        return $menu;
    }
}