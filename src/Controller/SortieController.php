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
    #[Route("/list", name: 'list', methods: ['GET', 'POST'])]
    public function list(
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        Request                $request
    )
    {
        // TODO récupérer les éléments de recherche dans la request
        // TODO récupére les éléments en fonction de la recherche
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties
        ]);
    }


    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Route('/publish', name: 'publish', methods: ['POST'])]
    public function createOrUpdate(
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        EtatRepository         $etatRepository,
        Request                $request,
        ?int                   $id = null): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une sortie.');
        }

        if ($id === null) {
            $sortie = new Sortie();
        } else {
            $sortie = $sortieRepository->find($id);
            if (!$sortie) {
                throw $this->createNotFoundException('Sortie non trouvée');
            }
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $user
        ]);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($id === null) {
                $sortie->setOrganisateur($user);
                $sortie->setCampus($user->getCampus());
                $sortie->setEtat($etatRepository->findOneBy(["nom" => "En creation"]));
                $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' enregistrée en brouillon');
            } else {
                if (!$sortieForm->get('publier')->isClicked()) {
                    $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' modifiée');
                }
            }
            if ($sortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatRepository->findOneBy(["nom" => "Ouverte"]));
                $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' est publiée');

            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        }
        if ($id === null) {
            return $this->render('sortie/create.html.twig', ['sortieForm' => $sortieForm]);
        } else {
            return $this->render('sortie/update.html.twig', ['sortieForm' => $sortieForm]);
        }

    }

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(
        int              $id,
        Request                $request,
        SortieRepository $sortieRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une sortie.');
        }

        if ($id === null) {
            $sortie = new Sortie();
        } else {
            $sortie = $sortieRepository->find($id);
            if (!$sortie) {
                throw $this->createNotFoundException('Sortie non trouvée');
            }
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $user
        ]);
        $sortieForm->handleRequest($request);

        return $this->render('sortie/detail.html.twig',['sortie'=>$sortie,'sortieForm' => $sortieForm]);
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
