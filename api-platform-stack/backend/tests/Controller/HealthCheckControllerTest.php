<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckControllerTest extends WebTestCase
{
    public function testHealthCheck()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/health-check'
        );
        $this->assertTrue($client->getResponse()->isOk());
    }
}
