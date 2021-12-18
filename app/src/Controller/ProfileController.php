<?php

namespace App\Controller;

use App\Form\EditProfileFormType;
use App\Repository\UserRepository;
use App\Utils\FileSaver;
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
    public function edit(Request $request, FileSaver $fileSaver, UserRepository $userRepository): Response
    {
        $email= $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['email' => $email]);

        $form = $this->createForm(EditProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form->get('photo')->getData();

            if ($uploadedFile){
                $photo = $fileSaver->save($uploadedFile);
                $user->setPhoto($photo);
            }

            $userRepository->save($user);

            return $this->redirectToRoute('main_profile_index');
        }

        return $this->render('main/profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
