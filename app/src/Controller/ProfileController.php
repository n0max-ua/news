<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Utils\FileSaver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @param Request $request
     * @param FileSaver $fileSaver
     * @param EntityManagerInterface $entityManager
     * @return Response
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

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
