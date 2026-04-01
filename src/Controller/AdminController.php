<?php

namespace App\Controller;

use App\Dto\UserImport;
use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\CampusSearchType;
use App\Form\CampusType;
use App\Form\Model\CampusSearch;
use App\Form\Model\VilleSearch;
use App\Form\ParticipantType;
use App\Form\UserImportType;
use App\Form\VilleSearchType;
use App\Repository\CampusRepository;
use App\Form\VilleType;
use App\Repository\ParticipantRepository;
use App\Repository\VilleRepository;
use App\Service\ImportParticipantService;
use App\utils\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    // Gestion des villes dans la page dédiée
    #[Route('/villes/list', name: 'villes_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function villes_list(
        EntityManagerInterface $entityManager,
        villeRepository        $villeRepository,
        Request                $request): Response
    {

        $villeSearch = new VilleSearch();
        $villeNew = new Ville();
        $ville = $villeRepository->findAll();

        $villeForm = $this->createForm(VilleSearchType::class, $villeSearch);
        $villeForm2 = $this->createForm(VilleType::class, $villeNew);


        if ($request->request->has('search')) {
            $villeForm->handleRequest($request);
            if ($villeForm->isSubmitted() && $villeForm->isValid()) {
                $villeSearch = $villeForm->getData();
                $ville = $villeRepository->findVillesBySearch($villeSearch);
            }
        }

        if ($request->request->has('create')) {
        $villeForm2->handleRequest($request);

        if ($villeForm2->isSubmitted() && $villeForm2->isValid()) {
            $villeNew = $villeForm2->getData();
            $entityManager->persist($villeNew);
            $entityManager->flush();
            return $this->redirectToRoute('admin_villes_list');
        }}
        return $this->render('admin/villes_list.html.twig', ['ville' => $ville, 'villeForm' => $villeForm, 'villeForm2' => $villeForm2]);
    }


    // Gestion des campus dans la page dédiée
    #[Route('/campus/list', name: 'campus_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function campus_list(
        EntityManagerInterface $entityManager,
        Request                $request,
        CampusRepository       $campusRepository
    ): Response
    {
        $campusSearch = new CampusSearch();
        $campusNew = new Campus();

        $campusForm = $this->createForm(CampusSearchType::class, $campusSearch);
        $campusForm2 = $this->createForm(CampusType::class, $campusNew);

        $campus = $campusRepository->findAll();

        if ($request->request->has('search')) {
            $campusForm->handleRequest($request);
            if ($campusForm->isSubmitted() && $campusForm->isValid()) {
                $campusSearch = $campusForm->getData();
                $campus = $campusRepository->findCampusBySearch($campusSearch);
            }
        }

        if ($request->request->has('create')) {
            $campusForm2->handleRequest($request);
            if ($campusForm2->isSubmitted() && $campusForm2->isValid()) {
                $campusNew = $campusForm2->getData();
                $entityManager->persist($campusNew);
                $entityManager->flush();
                return $this->redirectToRoute('admin_campus_list');
            }
        }
        return $this->render('admin/campus_list.html.twig', ['campus' => $campus, 'campusForm' => $campusForm, 'campusForm2' => $campusForm2]);
    }


    #[Route('/manage/user', name: 'manage_user', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function manage_user(): Response
    {
        return $this->render('admin/manage_user.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }


    #[Route('/new/users', name: 'new_user_csv', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ImportParticipantService $importParticipantService): Response
    {
        //nouveau dto
        $import = new UserImport();
        //crée le formulaire
        $form = $this->createForm(UserImportType::class, $import);
        //récupère le post
        $form->handleRequest($request);
        //validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
        //envoie au service
            $results = $importParticipantService->importFromCsv($import);

            $this->addFlash('success', sprintf(
                'Import OK : %d créés, %d erreurs. Mot de passe par défaut : %s',
                $results['created'], $results['errors'], $results['password']));
            return $this->redirectToRoute('admin_new_user_csv');
        }

        return $this->render('admin/participant_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new/user', name: 'new_user', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createOne(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader): Response
    {
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        $participantForm->handleRequest($request);
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $plainPassword = $participantForm->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($participant, $plainPassword);
                $participant->setPassword($hashedPassword);
            }
            // uploade de l'image
            /**
             * @var UploadedFile $file
             */
            $photoFile = $participantForm->get('photo')->getData();
            if ($photoFile) {
                $participant->setPhoto($fileUploader->upload($photoFile, 'images/profil'));
            }
            $participant->setRoles(['ROLE_USER']);
            // enregistrement en bdd
            $entityManager->persist($participant);
            $entityManager->flush();
            // messages flash pour utilisateurs
            $this->addFlash('success', 'Participant created successfully');
            return $this->redirectToRoute('admin_new_user');
        }

        return $this->render('admin/participant_create_unique.html.twig', [
            'participant' => $participant, 'form' => $participantForm->createView(),
        ]);
    }

    #[Route('/manage/deleteOrInactive', name: 'deleteOrInactive', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteOrInactive(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        //trouve tous les utilisateurs
        $participants = $participantRepository->findAll();
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $id = (int)$request->request->get('id');
            $participant = $participantRepository->find($id);
            if (!$participant) {
                $this->addFlash('error', 'User introuvable');
            } else {
                match ($action) {
                    'activate' => $participant->setActif(true),
                    'inactive' => $participant->setActif(false),
                    'delete' => $entityManager->remove($participant),
                    default => null
                };
                $entityManager->flush();
                $this->addFlash('success', match ($action) {
                    'activate' => 'User activé',
                    'inactive' => 'User désactivé',
                    'delete' => 'User supprimé (avec ses sorties)'
                });
            }
            return $this->redirectToRoute('admin_deleteOrInactive');
        }

        return $this->render('admin/participant_deleteOrInactive.html.twig', [
            'participants' => $participants,
        ]);
    }
}
