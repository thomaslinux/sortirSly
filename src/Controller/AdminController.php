<?php

namespace App\Controller;

use App\Dto\UserImport;
use App\Form\UserImportType;
use App\Repository\ParticipantRepository;
use App\Service\ImportParticipantService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    #[Route('/villes/list', name: 'villes_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function villes_list(): Response
    {
        return $this->render('admin/villes_list.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/campus/list', name: 'campus_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function campus_list(): Response
    {
        return $this->render('admin/campus_list.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/new', name: 'app_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ImportParticipantService $importParticipantService): Response
    {
//        nouveau dto
        $import = new UserImport();
//        crée le formulaire
        $form = $this->createForm(UserImportType::class, $import);
//        récupere le post
        $form->handleRequest($request);
//validation du formulaire
        if ($form->isSubmitted() && $form->isValid()){
//            fichier uploadé
            $csvFile = $import->csvFile;
//            envoie au service
            $results = $importParticipantService->importFromCsv($csvFile);

            $this->addFlash('success', sprintf(
                'Import OK : %d créés, %d erreurs. Mot de passe par défaut : %s',
                $results['created'], $results['errors'], $results['password']));
            return $this->redirectToRoute('admin_app_new');
        }

        return $this->render('admin/participant_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
