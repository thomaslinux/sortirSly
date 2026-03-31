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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/sortie/api/lieux', name: "api_lieux_")]
final class LieuController extends AbstractController
{
    #[Route('', name: 'retrieve_all', methods: 'GET')]
    public function retrieveAll(

        LieuRepository $lieuRepository): Response
    {
        $lieux = $lieuRepository->findAll();
        return $this->json($lieux, Response::HTTP_OK, [], ['groups' => 'lieux-api']);
    }

    #[Route('/{id}', name: 'retrieve_one', methods: ['GET'])]
    public function retrieveOne(SerializerInterface $serializer,
                                VilleRepository     $villeRepository,
                                int                 $id): Response
    {
        $ville = $villeRepository->find($id);
        return $this->json($ville, Response::HTTP_OK, [], ['groups' => 'villes-api']);
    }

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


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $entityManager,
        VilleRepository        $villeRepository,
        int                    $id
    ): Response
    {
        $ville = $villeRepository->find($id);

        $entityManager->remove($ville);
        $entityManager->flush();
        return $this->json(['success' => 'Ville supprimée!'], Response::HTTP_ACCEPTED);

    }

    #[Route('/{id}', name: 'update', methods: ['Put', 'PATCH'])]
    public function update(
        Request                $request,
        EntityManagerInterface $entityManager,
        VilleRepository        $villeRepository,
        int                    $id
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
