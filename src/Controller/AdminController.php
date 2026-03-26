<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    #[Route('/villes/list', name: 'villes_list')]
    public function villes_list(): Response
    {
        return $this->render('admin/villes_list.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/campus/list', name: 'campus_list')]
    public function campus_list(): Response
    {
        return $this->render('admin/campus_list.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/new', name: 'app_new')]
    public function create(Request $request, EntityManagerInterface $entityManager, ): Response
    {




        return $this->render('admin/participant_create.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
