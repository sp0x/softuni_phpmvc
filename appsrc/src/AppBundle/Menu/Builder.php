<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 29-Apr-17
 * Time: 3:02 PM
 */

namespace AppBundle\Menu;


use AppBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Intl\Intl;

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

    /**
     * @return User
     */
    private function getUser(){
        $tokens = $this->container->get('security.token_storage');
        return $tokens->getToken()->getUser();
    }

    public function rightMenu(FactoryInterface $factory, array $options){
        $menu = $factory->createItem('root');
        //If the user is logged
        if($this->isLoggedIn()){
            $currency = Intl::getCurrencyBundle()->getCurrencySymbol('EUR');
            $user = $this->getUser();
            $menu->addChild($user->getUsername() , array('route' => 'user_current'));
            $menu->addChild('My Cart' , array('route' => 'list_cart'));
            $menu->addChild("Credit: {$user->getCash()} {$currency}", array('route'=> 'list_cart'));
                //$menu[$user->getUsername()]->addChild("Credit: {$user->getCash()}", array('route'=>'mycart'));
            $menu[$user->getUsername()]->addChild('My purchases', array('route' => 'sales_user_sales'));
            $menu[$user->getUsername()]->addChild('Logout', array('route' => 'logout'));

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
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn){
            $user = $this->getUser();
            $routes = $this->container->get('router')->getRouteCollection();
            if($user->isAdmin()){
                $menu->addChild('Users', array('route' => 'user_list') );
                $menu->addChild('Sales', array('route' => 'sales_index') );
            }
            if($user->isAdmin() || $user->isEditor()){
                $menu->addChild('Categories', array('route' => 'category_list') );
                $menu->addChild('Promotions', array('route' => 'promotion_index') );
            }
            $menu->addChild('Products', array('route' => 'product_index') );
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