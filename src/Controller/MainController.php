<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class MainController extends AbstractController
{
    #[Route('', name: 'main_home')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('main/home.html.twig');
    }
}

