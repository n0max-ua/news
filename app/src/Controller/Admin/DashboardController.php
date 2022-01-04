<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractDashboardController
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $inactiveUsers = $this->userRepository->findBy(['is_active' => false]);
        return $this->render('_embed/_welcome-admin.html.twig', [
            'inactiveUsers' => $inactiveUsers
        ]);
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Panel');
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Posts', 'fa fa-circle', Post::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-circle', User::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-circle', Category::class);

        yield MenuItem::section('<hr>');
        yield MenuItem::linkToRoute('Back to homepage', 'fa fa-home', 'homepage');

        yield MenuItem::section('');
        yield MenuItem::linkToLogout('LogOut', 'fa fa-sign-out-alt');
    }
}
