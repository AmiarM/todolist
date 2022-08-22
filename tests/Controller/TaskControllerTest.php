<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testAuthPageIsRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $this->assertResponseRedirects('/login_check');
    }
}
