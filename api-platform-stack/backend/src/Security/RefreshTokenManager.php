<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenManager
{
    const REFRESH_TOKEN = 'refreshToken';
    const REFRESH_TOKEN_LIFETIME = 31536000;
    const ID_FIELD = 'username';
    const CREATION_DATE_FIELD = 'iat';
    const EXPIRATION_DATE_FIELD = 'exp';
    const TEN_YEARS = 315360000;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @param UserInterface $user
     * @return Cookie
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function createCookie(UserInterface $user)
    {
        $refreshToken = $this->jwtEncoder->encode([
            self::ID_FIELD => $user->getUsername(),
            self::CREATION_DATE_FIELD => time(),
            self::EXPIRATION_DATE_FIELD => time() + self::TEN_YEARS
        ]);

        return new Cookie(self::REFRESH_TOKEN, $refreshToken, 0, '/', null, true);
    }
}
