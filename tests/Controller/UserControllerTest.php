<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase

{
    public function testAuthPageIsRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertResponseRedirects('/login_check');
    }

    // public function testAdminRequireAdminRole()
    // {
    //     $client = static::createClient();
    //     $client->request('GET', '/users');
    //     $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    // }
}
