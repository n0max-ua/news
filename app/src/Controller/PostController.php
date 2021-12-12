<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\EditPostFormType;
use App\Repository\PostRepository;
use App\Utils\FileSaver;
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
    public function edit(Request $request, PostRepository $postRepository, FileSaver $fileSaver, Post $post = null): Response
    {
        if (!$post){
            $post = New Post();
        }

        $form = $this->createForm(EditPostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form->get('image')->getData();
            $image = $fileSaver->save($uploadedFile);
            $post->setImage($image);

            $user = $this->getUser();
            $postRepository->setSave($post, $user);

            return $this->redirectToRoute('main_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('main/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete{id}", name="delete")
     */
    public function delete(Post $post, PostRepository $postRepository): Response
    {
        $postRepository->setDelete($post);

        return $this->redirectToRoute('main_post_list');
    }
}
