<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class UserLastActionSubscriber implements EventSubscriberInterface
{
    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['setUserLastAction', 1],
            ],
        ];
    }

    public function setUserLastAction(RequestEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user || !method_exists($user, 'setLastActionAt')) {
            return;
        }

        $user->setLastActionAt(new DateTime());

        $this->em->flush();
    }
}