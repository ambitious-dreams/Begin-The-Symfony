<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Events\UserCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailNewUserSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $translator;
    private $sender;

    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator, $sender)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->sender = $sender;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'onUserCreated',
        ];
    }

    public function onUserCreated(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $password = $event->getPassword();

        $subject = $this->translator->trans('mail.user_created');
        $body = $this->translator->trans('mail.user_created.description', [
            '%email%' => $user->getEmail(),
            '%password%' => $password,
        ]);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setTo($user->getEmail())
            ->setFrom($this->sender)
            ->setBody($body, 'text/html')
        ;

        $this->mailer->send($message);
    }
}
