<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerControllerTest extends WebTestCase
{
    public function testAddproduct()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/add');
    }

    public function testRemoveproduct()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/remove');
    }

    public function testModifyproduct()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modify');
    }

    public function testCheckout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkout');
    }

}
