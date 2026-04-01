<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\utils\FileUploader;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/participant', name: 'participant_')]
#[IsGranted('ROLE_USER')]
final class ParticipantController extends AbstractController
{
    // route pour consultation des autres profils sans modification possible
    #[Route('/profil/{id}', name: 'profil', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function profile(
        int                   $id,
        ParticipantRepository $participantRepository): Response
    {
        // récuperation du participant par son id
        $participant = $participantRepository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant introuvable en base de données');
        }

        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }


    // route pour aller et modifier via un formulaire le profil de l'utilisateur
    #[Route('/update', name: 'update', methods: ['POST', 'GET'])]
    public function update(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        FileUploader                $fileUploader): Response
    {
        // récuperation du participant par son id
        $participant = $this->getUser();

        // création du formulaire d'update
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        // extraction des données de la requête
        $participantForm->handleRequest($request);

        // test de soumission et validation
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {
            // récuperation du mot de passe pour hashage
            $plainPassword = $participantForm->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($participant, $plainPassword);
                $participant->setPassword($hashedPassword);
            }
            // upload de l'image
            /**
             * @var UploadedFile $file
             */
            $photoFile = $participantForm->get('photo')->getData();
            if ($photoFile) {
                $participant->setPhoto($fileUploader->upload($photoFile, 'images/profil'));
            }

            // enregistrement en BdD
            $entityManager->persist($participant);
            $entityManager->flush();

            // messages flash pour utilisateur
            $this->addFlash('success', 'Votre profil est modifié!');
            return $this->redirectToRoute('main_home');
        }
        return $this->render('participant/update.html.twig', [
            'participant' => $participant, 'form' => $participantForm->createView(),
        ]);
    }
}
