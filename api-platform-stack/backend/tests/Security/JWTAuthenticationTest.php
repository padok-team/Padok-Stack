<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\RefreshTokenManager;
use App\Tests\AuthenticationTrait;
use App\Tests\FixtureAwareCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthenticationTest extends WebTestCase
{
    use FixtureAwareCaseTrait;

    use AuthenticationTrait;

    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
        static::loadFixtures('jwt_authentication.yaml');
    }

    /**
     * @test
     */
    public function itShouldAuthenticateTheUser()
    {
        // Without any JWT, the request should be unauthorized

        $this->client->request(
            'GET',
            '/users'
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        // With a JWT, it should pass
        $authenticatedClient = $this->authenticateClient($this->client, 'jean_mousquetaire', 'lolilol');

        $this->assertNotEmpty(array_filter(
            $authenticatedClient->getResponse()->headers->getCookies(),
            function ($cookie) {
                return $cookie->getName() === RefreshTokenManager::REFRESH_TOKEN && $cookie->getValue() !== null;
            }
        ));

        $authenticatedClient->request(
            'GET',
            '/users'
        );
        $this->assertTrue($authenticatedClient->getResponse()->isOk());
        $this->assertFalse($authenticatedClient->getResponse()->isEmpty());
    }

    /**
     * @test
     */
    public function itShouldRefreshAccessToken()
    {
        // Without refreshToken, the request should be unauthorized
        $this->client->request(
            'POST',
            '/auth/jwt/refresh'
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        // With a refreshToken, it should pass
        $authenticatedClient = $this->authenticateClient($this->client, 'jean_mousquetaire', 'lolilol');

        $authenticatedClient->request(
            'POST',
            '/auth/jwt/refresh'
        );

        $this->assertTrue($authenticatedClient->getResponse()->isOk());
        $this->assertContains('access' , $authenticatedClient->getResponse()->getContent());
    }

    /**
     * @test
     */
    public function itShouldLogoutTheUser()
    {
        $authenticatedClient = $this->authenticateClient($this->client, 'jean_mousquetaire', 'lolilol');

        $this->assertNotEmpty(array_filter(
            $authenticatedClient->getResponse()->headers->getCookies(),
            function ($cookie) {
                return $cookie->getName() === RefreshTokenManager::REFRESH_TOKEN && $cookie->getValue() !== null;
            }
        ));

        $authenticatedClient->request(
            'POST',
            '/auth/jwt/logout'
        );

        $this->assertEmpty(array_filter(
            $authenticatedClient->getResponse()->headers->getCookies(),
            function ($cookie) {
                return $cookie->getName() === RefreshTokenManager::REFRESH_TOKEN && $cookie->getValue() !== null;
            }
        ));
    }
}
