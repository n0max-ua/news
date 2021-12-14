<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_homepage")
     */
    public function index(): Response
    {
        return $this->render('main/default/index.html.twig');
    }

    /**
     * @Route("/news", name="main_news")
     */
    public function news(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(
            ['status' => 2],
            ['posted_at' => 'ASC'],
            50
        );

        return $this->render('main/default/news.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/news/{id}", name="main_news_item")
     */
    public function item(Post $post): Response
    {
        return $this->render('main/default/news_item.html.twig',[
            'post' => $post
        ]);
    }
}
