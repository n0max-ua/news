<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddUserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:add-user';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Create user';

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param string|null $name
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        string $name = null,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'em', InputArgument::REQUIRED, 'Email')
            ->addOption('password', 'p', InputArgument::REQUIRED, 'Password')
            ->addOption(
                'isAdmin',
                '',
                InputArgument::OPTIONAL,
                'Set if user is admin',
                0
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        $io->title('Create User Command');
        $io->text('Please, enter some information');

        if (!$email) {
            $email = $io->ask('Email');
        }

        if (!$password) {
            $password = $io->askHidden('Password (hidden)');
        }

        if (!$isAdmin) {
            $question = new Question('Is admin? (1 or 0)', 0);
            $isAdmin = $io->askQuestion($question);
        }

        $isAdmin = boolval($isAdmin);

        try {
            $user = $this->createUser($email, $password, $isAdmin);
        } catch (RuntimeException $exception) {
            $io->comment($exception->getMessage());

            return Command::FAILURE;
        }

        $successMessage = sprintf(
            '%s was created as %s',
            $user->getEmail(),
            $isAdmin ? 'Admin' : 'User'
        );
        $io->success($successMessage);

        return Command::SUCCESS;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $isAdmin
     * @return User
     */
    private function createUser(string $email, string $password, bool $isAdmin): User
    {
        $existedUser = $this->userRepository->findOneBy(['email' => $email]);

        if ($existedUser) {
            throw new RuntimeException('User already exist');
        }

        $user = new User();
        $user->setEmail($email);

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $user->setRoles([$isAdmin ? User::ROLE_ADMIN : User::ROLE_USER]);
        $user->setIsActive($isAdmin);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
