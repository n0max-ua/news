<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['status' => Post::STATUS_POSTED], ['posted_at' => 'DESC']);

        return $this->render('main/index.html.twig', [
            'post' => empty($posts) ? [] : $posts[array_rand($posts)]
        ]);
    }

    /**
     * @param Request $request
     * @param PostRepository $postRepository
     * @param PaginatorInterface $paginator
     * @return Response
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

        return $this->render('main/news.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @param Post $post
     * @return Response
     */
    public function item(Post $post): Response
    {
        return $this->render('main/news-item.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @param PostRepository $postRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function category(
        Request $request,
        Category $category,
        PostRepository $postRepository,
        PaginatorInterface $paginator
    ): Response {
        $query = $postRepository->findBy([
            'category' => $category->getId(),
            'status' => Post::STATUS_POSTED
        ]);

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('main/category.html.twig', [
            'category' => $category,
            'posts' => $posts
        ]);
    }
}
