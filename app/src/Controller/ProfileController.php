<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Utils\FileSaver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="main_profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('main/profile/index.html.twig');
    }

    /**
     * @param Request $request
     * @param FileSaver $fileSaver
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/edit", name="edit")
     */
    public function edit(Request $request, FileSaver $fileSaver, EntityManagerInterface $entityManager): Response
    {
        /**@var User $user */
        $user = $this->getUser();
        $oldPhoto = $user->getPhoto();

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('photo')->getData();

            if ($uploadedFile) {
                if ($oldPhoto) {
                    $fileSaver->removeImage($oldPhoto);
                }
                $newPhoto = $fileSaver->saveImage($uploadedFile);
                $user->setPhoto($newPhoto);
            }

            $entityManager->flush();

            return $this->redirectToRoute('main_profile_index');
        }

        return $this->render('main/profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
