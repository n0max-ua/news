<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('main/_embed/_welcome-admin.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Panel');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Post');
        yield MenuItem::linkToCrud('All', 'far fa-file', Post::class);

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('All', 'fa fa-users', User::class);

        yield MenuItem::section('Categories');
        yield MenuItem::linkToCrud('All', 'fa fa-dot-circle', Category::class);

        yield MenuItem::section('<hr>');
        yield MenuItem::section('');
        yield MenuItem::linkToRoute('Back to homepage', 'fa fa-home', 'main_homepage');

        yield MenuItem::section('');
        yield MenuItem::linkToLogout('LogOut', 'fa fa-sign-out-alt');
    }
}
