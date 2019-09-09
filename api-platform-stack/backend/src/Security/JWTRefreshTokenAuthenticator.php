<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidPayloadException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JWTRefreshTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**.
     * @var AuthenticationSuccessHandler
     */
    private $authenticationSuccessHandler;

    public function __construct(
        JWTEncoderInterface $jwtEncoder,
        AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
        $this->jwtEncoder                    = $jwtEncoder;
        $this->authenticationSuccessHandler  = $authenticationSuccessHandler;
    }

    public function supports(Request $request)
    {
        return false !== $request->cookies->get(RefreshTokenManager::REFRESH_TOKEN, false);
    }

    /**
     * Returns a decoded JWT token extracted from a request.
     *
     * {@inheritdoc}
     *
     * @throws InvalidTokenException If an error occur while decoding the token
     * @throws ExpiredTokenException If the request token is expired
     */
    public function getCredentials(Request $request)
    {
        $jsonWebToken = $request->cookies->get(RefreshTokenManager::REFRESH_TOKEN);

        try {
            if (!$payload = $this->jwtEncoder->decode($jsonWebToken)) {
                throw new InvalidTokenException('Invalid JWT Token');
            }
        } catch (JWTDecodeFailureException $e) {
            if (JWTDecodeFailureException::EXPIRED_TOKEN === $e->getReason()) {
                throw new ExpiredTokenException();
            }

            throw new InvalidTokenException('Invalid JWT Token', 0, $e);
        }

        return $payload;
    }

    /**
     * Returns an user object loaded from a JWT token.
     *
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If preAuthToken is not of the good type
     * @throws InvalidPayloadException   If the user identity field is not a key of the payload
     * @throws UserNotFoundException     If no user can be loaded from the given token
     */
    public function getUser($refreshToken, UserProviderInterface $userProvider)
    {
        $idField = RefreshTokenManager::ID_FIELD;

        if (!isset($refreshToken[$idField])) {
            throw new InvalidPayloadException($idField);
        }

        $identity = $refreshToken[$idField];

        try {
            $user = $userProvider->loadUserByUsername($identity);
        } catch (UsernameNotFoundException $e) {
            throw new UserNotFoundException($idField, $identity);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $authException)
    {
        return new JWTAuthenticationFailureResponse($authException->getMessageKey());
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            throw new AuthenticationException();
        }

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
    }

    /**
     * {@inheritdoc}
     *
     * @return JWTAuthenticationFailureResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JWTAuthenticationFailureResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($refreshToken, UserInterface $user)
    {
        if ($refreshToken[RefreshTokenManager::CREATION_DATE_FIELD] + RefreshTokenManager::REFRESH_TOKEN_LIFETIME <= time()) {
            throw new ExpiredTokenException();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
