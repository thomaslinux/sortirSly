<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route("/sortie", name: 'sortie_')]
final class SortieController extends AbstractController
{
    #[Route('/create', name: 'create',methods: ['GET', 'POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Sortie '.$sortie->getNom().' enregistrée en brouillon');
            return $this->redirectToRoute('main_home',['id'=>$sortie->getId()]);
        }
        return $this->render('sortie/create.html.twig',['sortieForm'=>$sortieForm] );
    }


    #[Route('/detail/{id}', name: 'detail')]
    public function detail(): Response
    {
        return $this->render('sortie/detail.html.twig' );
    }


    #[Route('/update/{id}', name: 'update')]
    public function update(): Response
    {
        return $this->render('sortie/update.html.twig' );
    }


    #[Route('/publish/{id}', name: 'publish')]
    public function publish(): Response
    {
        return $this->render('sortie/create.html.twig' );
    }


    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('sortie/cancel.html.twig' );
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(): Response
    {
        return $this->redirectToRoute('main_home');
    }
































}
