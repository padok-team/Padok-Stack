<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler as LexikAuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**.
     * @var LexikAuthenticationSuccessHandler
     */
    private $lexikAuthenticationSuccessHandler;

    /**
     * @var RefreshTokenManager
     */
    private $refreshTokenManager;

    public function __construct(JWTTokenManagerInterface $jwtManager, LexikAuthenticationSuccessHandler $lexikAuthenticationSuccessHandler, RefreshTokenManager $refreshTokenManager)
    {
        $this->jwtManager = $jwtManager;
        $this->lexikAuthenticationSuccessHandler = $lexikAuthenticationSuccessHandler;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        if(!$user instanceof UserInterface) {
            throw new AuthenticationException();
        }

        $response = $this->lexikAuthenticationSuccessHandler->handleAuthenticationSuccess($user);

        $response->headers->setCookie($this->refreshTokenManager->createCookie($user));

        return $response;
    }
}
