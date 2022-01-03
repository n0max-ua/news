<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ActivityListener implements EventSubscriberInterface{

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onResponse'
        ];
    }

    public function onResponse()
    {
        $user = $this->security->getUser();

        if ($user){
            /** @var User $user */
            $user->setLastActivity(new \DateTimeImmutable());
        }
    }
}