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

#[Route('/participant', name: 'participant_')]
final class ParticipantController extends AbstractController
{
    //routes pour consulation des autres profil sans modification posibles
    #[Route('/profil/{id}', name: 'profil', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function profile(int $id, ParticipantRepository $participantRepository): Response
    {
//        récuperation du participant par son id
        $participant = $participantRepository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant not found');
        }

        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
             ]);
    }

    // routes pour aller et modifier via un formulaire le profil de l'utilisateur
    #[Route('/update', name: 'update', methods: ['POST','GET'])]
    public function update(ParticipantRepository $participantRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        //        récuperation du participant par son id
        $participant = $this->getUser();
        //        création du formulaire d'update
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        //        extraction des données de la requete
        $participantForm->handleRequest($request);

        // test de soumission et validation
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {
            //récuperation du motdepasse pour hashage
            $plainPassword = $participantForm->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($participant, $plainPassword);
                $participant->setPassword($hashedPassword);
            }
            // enregistrement en bdd
            $entityManager->persist($participant);
            $entityManager->flush();
            // messages flash pour utilisateurs
            $this->addFlash('success', 'Participant updated successfully');
            return $this->redirectToRoute('main_home');
        }
        return $this->render('participant/update.html.twig', [
            'participant' => $participant, 'form' => $participantForm->createView(),
        ]);
    }
}
