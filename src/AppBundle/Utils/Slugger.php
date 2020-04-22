<?php
/**
 * Created by IntelliJ IDEA.
 * User: cyb3r
 * Date: 24-Apr-17
 * Time: 9:35 PM
 */

namespace AppBundle\Utils;


class Slugger
{
    public function __construct()
    {
    }

    public function slugify($string){
        return preg_replace('/[^a-z0-9]/', '-', strtolower(trim(strip_tags($string))));
    }


}