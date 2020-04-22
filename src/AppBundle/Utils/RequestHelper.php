<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 24-Apr-17
 * Time: 9:35 PM
 */

namespace AppBundle\Utils;


use Symfony\Component\HttpFoundation\RequestStack;

class RequestHelper
{

    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCurrent(){
        $request = $this->requestStack->getCurrentRequest();
        return $request;
    }
    public function getCurrentRoute(){
        $request = $this->requestStack->getCurrentRequest();
        return $request->get('_route');
    }

}