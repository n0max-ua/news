<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use App\Utils\FileSaver;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @param Request $request
     * @param PostRepository $postRepository
     * @param $id
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/status/{id}", name="status")
     */
    public function status(Request $request, PostRepository $postRepository, $id, PaginatorInterface $paginator): Response
    {
        if (!in_array($id, Post::getStatuses())) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig');
        }

        $query = $postRepository->findBy([
            'status' => $id,
            'user' => $this->getUser()
        ]);

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('main/post/list.html.twig', [
            'posts' => $posts,
            'statuses' => Post::getStatuses()
        ]);
    }

    /**
     * @param Request $request
     * @param FileSaver $fileSaver
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/add", name="add")
     */
    public function add(Request $request, FileSaver $fileSaver, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                $image = $fileSaver->saveImage($uploadedFile);
                $post->setImage($image);
            }

            /**@var User $user */
            $user = $this->getUser();
            $post->setUser($user);

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'You create new post!');

            return $this->redirectToRoute('main_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('main/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param FileSaver $fileSaver
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, FileSaver $fileSaver, Post $post, EntityManagerInterface $entityManager): Response
    {
        $oldImage = $post->getImage();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                if ($oldImage) {
                    $fileSaver->removeImage($oldImage);
                }
                $newImage = $fileSaver->saveImage($uploadedFile);
                $post->setImage($newImage);
            }

            $post->setStatus(Post::STATUS_CREATED);
            $entityManager->flush();

            $this->addFlash('success', 'You edit the post!');

            return $this->redirectToRoute('main_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('main/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        $post->setStatus(3);
        $post->setDeletedAt(new \DateTimeImmutable());
        $entityManager->flush();

        return $this->redirectToRoute('main_post_status', ['id' => 1]);
    }
}
