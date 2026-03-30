<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lieu', name: 'lieu_')]
final class LieuController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function createLieu(
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

    if($lieuForm->isSubmitted()){
        $this->addFlash('success', 'Votre lieu de sortie : ' . $lieu->getNom() . ' a été créé');

        $entityManager->persist($lieu);
        $entityManager->flush();
        return $this->redirectToRoute('sortie_create');
    }














        return $this->render('lieu/create.html.twig', [
            'lieuForm' => $lieuForm
        ]);
    }
}
