<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearOldDataCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:remove-old-users';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Remove inactive users and transfer their posts to the admin';

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;

    /**
     * @param string|null $name
     * @param UserRepository $userRepository
     * @param PostRepository $postRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        string $name = null,
        UserRepository $userRepository,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $now = new DateTimeImmutable();
        $targetDate = $now->sub(new DateInterval('P6M'));
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $lastActivity = $user->getLastActivity();

            if ($lastActivity < $targetDate) {
                $this->removePostsFromOldUserToAdmin($user);
                $this->entityManager->remove($user);
                $this->entityManager->flush();

                $io->success(sprintf('%s was deleted', $user->getEmail()));
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param User $user
     */
    private function removePostsFromOldUserToAdmin(User $user)
    {
        $admin = $this->userRepository->findAdmin();
        $posts = $this->postRepository->findBy(['user' => $user]);

        foreach ($posts as $post) {
            $post->setUser($admin);
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        }
    }
}
