<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends AbstractController
{
    /**
     * @param PostRepository $postRepository
     * @return Response
     */
    public function showLastNews(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['status' => Post::STATUS_POSTED], ['id' => 'DESC'], 10);

        return $this->render('main/_embed/_last_news.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function showCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(['is_deleted' => false]);

        return $this->render('main/_embed/_categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
