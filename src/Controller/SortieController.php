<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CancelSortieType;
use App\Form\Model\CancelSortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
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
        // TODO afficher les résultats de la recherche
        // TODO changer la requête sur mobile (campus utilisateur)
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties
        ]);
    }

// code commum pour les 3 fonctions de création, de modification et de publication
// Pour la publication => ne fonctionne que dans les pages de création ou de modification (voir publishID pour différence)
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
        if ($id === null) {
            $userAgent = $request->headers->get('User-Agent');
            if (preg_match('/Mobile|Android|iPhone|BlackBerry|IEMobile/i', $userAgent ?? '')) {
                throw $this->createNotFoundException('Accès interdit sur mobile');
            }
            $sortie = new Sortie();
        } else {
            $sortie = $sortieRepository->find($id);
            if (!$sortie) {
                throw $this->createNotFoundException('Sortie non trouvée');
            }
        }

        $user = $this->getUser();


        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $user
        ]);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($id === null) {
                $sortie->setOrganisateur($user);
                $sortie->sIncrire($user);
                $sortie->setCampus($user->getCampus());
                $sortie->setEtat($etatRepository->findOneBy(["nom" => "En creation"]));
                $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' enregistrée en brouillon');
            } else {
                //si bouton enregister
                if (!$sortieForm->get('publier')->isClicked()) {
                    $this->denyAccessUnlessGranted('EDIT', $sortie);
                    $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' modifiée');
                }
            }
            //si bouton publier
            if ($sortieForm->get('publier')->isClicked()) {
                $this->denyAccessUnlessGranted('PUBLISH', $sortie);
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
            return $this->render('sortie/update.html.twig', ['sortieForm' => $sortieForm, 'sortie' => $sortie]);
        }

    }

// affichage de la page de détail d'une sortie
    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(
        int              $id,
        SortieRepository $sortieRepository): Response
    {

        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $this->denyAccessUnlessGranted('VIEW', $sortie);
        return $this->render('sortie/detail.html.twig', ['sortie' => $sortie]);
    }

    // Permet d'annuler une sortie lorsqu'elle est "Ouverte" : passe en statut annulé
    #[Route('/cancel/{id}', name: 'cancel', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function cancel(
        int                    $id,
        Request                $request,
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository,
        SortieRepository       $sortieRepository): Response
    {
        $userAgent = $request->headers->get('User-Agent');
        if (preg_match('/Mobile|Android|iPhone|BlackBerry|IEMobile/i', $userAgent ?? '')) {
            throw $this->createNotFoundException('Accès interdit sur mobile');
        }
        $user = $this->getUser();

        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $this->denyAccessUnlessGranted('CANCEL', $sortie);
        $cancelSortie = new CancelSortie();
        $cancelSortieForm = $this->createForm(CancelSortieType::class, $cancelSortie, [
            'user' => $user
        ]);
        $cancelSortieForm->handleRequest($request);

        if ($cancelSortieForm->isSubmitted() && $cancelSortieForm->isValid()) {
            $sortie->setEtat($etatRepository->findOneBy(["nom" => "Annulee"]));
            $sortie->setDescription($sortie->getDescription() . ".\nAnnulée pour le motif suivant : " . $cancelSortie->getDescriptionCancel());
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' annulée pour le motif suivant : ' . $cancelSortie->getDescriptionCancel());
            return $this->redirectToRoute('main_home');
        }
        return $this->render('sortie/cancel.html.twig', ['sortie' => $sortie, 'cancelSortieForm' => $cancelSortieForm]);
    }

//Permet de supprimer une sortie lorsqu'elle est "en création" : elle disparait de la BdD
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(
        int                    $id,
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        Request                $request
    ): Response
    {
        $userAgent = $request->headers->get('User-Agent');
        if (preg_match('/Mobile|Android|iPhone|BlackBerry|IEMobile/i', $userAgent ?? '')) {
            throw $this->createNotFoundException('Accès interdit sur mobile');
        }
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $this->denyAccessUnlessGranted('DELETE', $sortie);
        $entityManager->remove($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' supprimée!');
        return $this->redirectToRoute('main_home');
    }


    // Permet de publier une sortie à partir de la page de listing
    #[Route('/publish/{id}', name: 'publishId', methods: ['GET'])]
    public function publishId(
        int                    $id,
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository,
        SortieRepository       $sortieRepository): Response
    {

        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $this->denyAccessUnlessGranted('EDIT', $sortie);
        $sortie->setEtat($etatRepository->findOneBy(["nom" => "Ouverte"]));
        $this->addFlash('success', 'Sortie ' . $sortie->getNom() . ' est publiée');

        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }


    // inscription à une sortie
    #[Route('/subscribe/{id}', name: 'subscribe', requirements: ['id' => '\d+'])]
    public function subscribe(int                    $id,
                              EntityManagerInterface $entityManager,
                              SortieRepository       $sortieRepository): Response
    {

        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $this->denyAccessUnlessGranted('VIEW', $sortie);
        $this->denyAccessUnlessGranted('INS', $sortie);

        $user = $this->getUser();
        $sortie->sIncrire($user);
        $this->addFlash('success', 'Vous êtes inscrit à la sortie ' . $sortie->getNom());

        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_list');
    }

// se désister d'une sortie
    #[Route('/unsubscribe/{id}', name: 'unsubscribe', requirements: ['id' => '\d+'])]
    public function unsubscribe(int                    $id,
                                EntityManagerInterface $entityManager,
                                SortieRepository       $sortieRepository): Response
    {

        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $this->denyAccessUnlessGranted('VIEW', $sortie);
        $this->denyAccessUnlessGranted('DESINS', $sortie);
        $user = $this->getUser();
        $sortie->seDesister($user);
        $this->addFlash('success', 'Vous vous êtes désisté de la sortie ' . $sortie->getNom());

        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_list');
    }


}
