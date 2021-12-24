<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use App\Utils\FileSaver;
use Doctrine\Persistence\ManagerRegistry;
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
     * @Route("/status/{id}", name="status")
     */
    public function status(PostRepository $postRepository, $id): Response
    {
        if (!array_key_exists($id, Post::getStatuses())){
            return $this->render('bundles/TwigBundle/Exception/error.html.twig');
        }

        $posts = $postRepository->findBy([
            'status' => $id,
            'user' => $this->getUser()
        ]);

        return $this->render('main/post/list.html.twig', [
            'posts' => $posts,
            'statuses' => Post::getStatuses()
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, FileSaver $fileSaver, ManagerRegistry $managerRegistry): Response
    {

        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile){
                $image = $fileSaver->saveImage($uploadedFile);
                $post->setImage($image);
            }

            /**@var User $user*/
            $user = $this->getUser();
            $post->setUser($user);

            $managerRegistry->getManager()->persist($post);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('main_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('main/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, FileSaver $fileSaver, Post $post, ManagerRegistry $managerRegistry): Response
    {
        $oldImage = $post->getImage();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile){
                if ($oldImage){
                    $fileSaver->removeImage($oldImage);
                }
                $newImage = $fileSaver->saveImage($uploadedFile);
                $post->setImage($newImage);
            }

            $post->setStatus(Post::STATUS_CREATED);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('main_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('main/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(Post $post, ManagerRegistry $managerRegistry): Response
    {
        $post->setStatus(3);
        $post->setDeletedAt(new \DateTimeImmutable());
        $managerRegistry->getManager()->flush();

        return $this->redirectToRoute('main_post_status', ['id' => 1]);
    }

}
