<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function news(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        $query = $postRepository->findBy(
            ['status' => Post::STATUS_POSTED],
            ['posted_at' => 'ASC'],
            50
        );

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
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
        return $this->render('main/default/news-item.html.twig', [
            'post' => $post
        ]);
    }
    /**
     * @Route("/news/category/{id}", name="main_news_category")
     */
    public function category(Request $request, Category $category, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        $query = $postRepository->findBy([
            'category' => $category->getId(),
            'status' => Post::STATUS_POSTED
        ]);

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('main/default/category.html.twig', [
            'category' => $category,
            'posts' => $posts
        ]);
    }

}
