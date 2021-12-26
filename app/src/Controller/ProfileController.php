<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Repository\UserRepository;
use App\Utils\FileSaver;
use Doctrine\Persistence\ManagerRegistry;
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
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('main/profile/index.html.twig');
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit(Request $request, FileSaver $fileSaver, ManagerRegistry $managerRegistry): Response
    {
        /**@var User $user*/
        $user = $this->getUser();
        $oldPhoto = $user->getPhoto();

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form->get('photo')->getData();

            if ($uploadedFile){
                if ($oldPhoto){
                    $fileSaver->removeImage($oldPhoto);
                }
                $newPhoto = $fileSaver->saveImage($uploadedFile);
                $user->setPhoto($newPhoto);
            }

            $managerRegistry ->getManager()->flush();

            return $this->redirectToRoute('main_profile_index');
        }

        return $this->render('main/profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
