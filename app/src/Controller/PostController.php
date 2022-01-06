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

class PostController extends AbstractController
{
    /**
     * @param Request $request
     * @param PostRepository $postRepository
     * @param $id
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function status(Request $request, PostRepository $postRepository, $id, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getIsActive()){
            throw $this->createAccessDeniedException();
        }

        if (!in_array($id, Post::getStatuses())) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig');
        }

        $query = $postRepository->findBy([
            'status' => $id,
            'user' => $user
        ]);

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('post/list.html.twig', [
            'posts' => $posts,
            'statuses' => Post::getStatuses()
        ]);
    }

    /**
     * @param Request $request
     * @param FileSaver $fileSaver
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function add(Request $request, FileSaver $fileSaver, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getIsActive()){
            throw $this->createAccessDeniedException();
        }

        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                $image = $fileSaver->saveImage($uploadedFile);
                $post->setImage($image);
            }

            $post->setUser($user);

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'You create new post!');

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
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
     */
    public function edit(Request $request, FileSaver $fileSaver, Post $post, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getIsActive()){
            throw $this->createAccessDeniedException();
        }

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

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'You edit the post!');

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getIsActive()){
            throw $this->createAccessDeniedException();
        }

        $post->setStatus(Post::STATUS_DELETED);
        $post->setDeletedAt(new \DateTimeImmutable());

        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('post_status', ['id' => Post::STATUS_CREATED]);
    }
}
