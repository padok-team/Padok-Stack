<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [Events::AUTHENTICATION_SUCCESS => 'renameTokenField'];
    }

    public function renameTokenField(AuthenticationSuccessEvent $event)
    {
        if (!isset($event->getData()['token']))
        {
            throw new AuthenticationException();
        }

        $jwt = $event->getData()['token'];

        $event->setData(['access' => $jwt]);
    }
}
