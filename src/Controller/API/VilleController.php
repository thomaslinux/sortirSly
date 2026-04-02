<?php

namespace App\Controller\API;

use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// Controller API de gestion des "Villes"
#[Route('/api/villes', name: "api_villes_")]

final class VilleController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('', name: 'retrieve_all', methods: ['GET'])]
    public function retrieveAll(

        VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();
        return $this->json($villes, Response::HTTP_OK, [], ['groups' => 'villes-api']);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'retrieve_one', methods: ['GET'])]
    public function retrieveOne(
        VilleRepository $villeRepository,
        int             $id): Response
    {
        $ville = $villeRepository->find($id);
        return $this->json($ville, Response::HTTP_OK, [], ['groups' => 'villes-api']);
    }

    // c'est cette fonction qui permet de gérer les lieux dans l'ajout de lieux (créer une sortie)
    #[IsGranted("ROLE_USER")]
    #[Route('/{id}/lieux', name: 'api_ville_lieux', methods: ['GET'])]
    public function LieuxAll(
        VilleRepository $villeRepository,
        int             $id
    ): Response
    {
        $ville = $villeRepository->find($id);
        return $this->json($ville->getLieux(), Response::HTTP_OK, [], ['groups' => 'villes-api']);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(SerializerInterface    $serializer,
                           EntityManagerInterface $entityManager,
                           ValidatorInterface     $validator,
                           Request                $request): Response
    {
        $json = $request->getContent();
        $ville = $serializer->deserialize($json, Ville::class, 'json');
        $errors = $validator->validate($ville);
        if ($errors->count() == 0) {
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->json($ville, Response::HTTP_CREATED, [], ['groups' => 'villes-api']);
        } else {
            return $this->json($errors, Response::HTTP_CREATED, [], ['groups' => 'villes-api']);
        }
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $entityManager,
        VilleRepository        $villeRepository,
        int                    $id = null
    ): Response
    {
        $ville = $villeRepository->find($id);

        $entityManager->remove($ville);
        $entityManager->flush();
        return $this->json(['success' => 'Ville supprimée!'], Response::HTTP_ACCEPTED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        Request                $request,
        EntityManagerInterface $entityManager,
        VilleRepository        $villeRepository,
        int                    $id = null
    ): Response
    {
        $ville = $villeRepository->find($id);
        $data = json_decode($request->getContent(), true);

        if (!$ville) {
            return $this->json(['error' => 'Ville non trouvée'], Response::HTTP_NOT_FOUND);
        }
        if (isset($data['nom'])) {
            $ville->setNom($data['nom']);
        }
        if (isset($data['codePostal'])) {
            $ville->setCodePostal($data['codePostal']);
        }
        $entityManager->flush();
        return $this->json($ville, Response::HTTP_OK, [], ['groups' => 'villes-api']);
    }
}
