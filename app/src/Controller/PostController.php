<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\EditPostFormType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="main_post_")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['status' => 1]);

        return $this->render('main/post/list.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @Route("/add", name="add")
     */
    public function edit(Request $request, Post $post = null): Response
    {
        if (!$post){
            $post = New Post();
            $post->setUser($this->getUser());
        }
        $form = $this->createForm(EditPostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('main/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete{id}", name="delete")
     */
    public function delete(Post $post): Response
    {
        $post->setStatus(2);
        $post->setDeletedAt(new \DateTimeImmutable());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('main_post_list');
    }
}
