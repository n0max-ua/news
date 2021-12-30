<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends AbstractController
{
    public function showLastNews(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['status' => 2], ['id' =>'DESC'], 10);
        return $this->render('main/_embed/_last_news.html.twig', [
            'posts' => $posts
        ]);
    }

    public function showCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(['is_deleted' => false]);
        return $this->render('main/_embed/categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
