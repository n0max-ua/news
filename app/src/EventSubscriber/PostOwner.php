<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class PostOwner implements EventSubscriberInterface
{
    /**
     * @var Security $security
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
     * @return string[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setUser']
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function setUser(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Post) {
            /** @var User $user */
            $user = $this->security->getUser();

            $entity->setUser($user);
        }
    }
}
