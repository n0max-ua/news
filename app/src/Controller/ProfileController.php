<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="main_profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {

        return $this->render('main/profile/index.html.twig');
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit(): Response
    {

        return $this->render('main/profile/edit.html.twig');
    }
}
