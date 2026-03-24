<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route("/sortie", name: 'sortie_')]
final class SortieController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository,
        Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une sortie.');
        }

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->setCampus($this->getUser()->getCampus());
            $sortie->setEtat($etatRepository->findOneBy(["nom" => "En creation"]));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' enregistrée en brouillon');
            return $this->redirectToRoute('main_home', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/create.html.twig', ['sortieForm' => $sortieForm]);
    }


    #[Route('/detail/{id}', name: 'detail')]
    public function detail(): Response
    {
        return $this->render('sortie/detail.html.twig');
    }


    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(
        int                    $id,
        SortieRepository       $sortieRepository,
        EntityManagerInterface $entityManager,
        Request                $request): Response
    {
        $sortie = $sortieRepository->find($id);
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $this->getUser()
        ]);
        $sortieForm->handleRequest($request);

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une sortie.');
        }
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $etat = new Etat();
            $etat->setNom("En création");

            $sortie->setOrganisateur($this->getUser())->setNom($this->getUser()->getUserIdentifier());
            $sortie->setCampus($this->getUser()->getCampus());
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->persist($etat);

            $entityManager->flush();
            $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' modifiée');
            return $this->redirectToRoute('main_home', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/update.html.twig', ['sortieForm' => $sortieForm]);
    }


    #[Route('/publish/{id}', name: 'publish', requirements: ['id' => '/d+'], methods: ['GET', 'POST'])]
    public function publish(): Response
    {
        return $this->render('sortie/create.html.twig');
    }


    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('sortie/cancel.html.twig');
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(): Response
    {
        return $this->redirectToRoute('main_home');
    }


}
