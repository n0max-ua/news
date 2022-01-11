<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordEncoder implements EventSubscriberInterface
{
    /**
     * @var UserPasswordHasherInterface $userPasswordHasher
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @return string[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setPassword']
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function setPassword(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User) {
            $encodedPassword = $this->userPasswordHasher->hashPassword($entity, $entity->getPassword());
            $entity->setPassword($encodedPassword);
        }
    }
}