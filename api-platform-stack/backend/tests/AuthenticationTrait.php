<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Client;

trait AuthenticationTrait
{
    /**
     * Create a client with a default Authorization header.
     *
     * @param Client $client
     * @param string $email
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function authenticateClient(Client $client, string $email = 'jean_moust', string $password = 'lolilol'): Client
    {
        $client->request(
            'POST',
            '/auth/jwt/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            sprintf('{"email": "%s", "password": "%s"}', $email, $password)
        );

        $this->assertTrue($client->getResponse()->isOk(), 'Authentication failed, check that the provided credentials are valid and that the authentication works.');

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameters([
            'HTTP_Authorization' => sprintf('Bearer %s', $data['access']),
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ]);

        return $client;
    }
}
