<?php

namespace App\Controller\API;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// Controller API de gestion des "Campus"
#[Route('/api/campus', name: "api_campus_")]
#[IsGranted("ROLE_ADMIN")]
final class CampusController extends AbstractController
{
    #[Route('', name: 'retrieve_all', methods: 'GET')]
    public function retrieveAll(
        CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();
        return $this->json($campus, Response::HTTP_OK, [],['groups' => 'campus-api']);
    }


    #[Route('/{id}', name: 'retrieve_one', methods: ['GET'])]
    public function retrieveOne(
        CampusRepository $campusRepository,
        int              $id): Response
    {
        $campus = $campusRepository->find($id);
        return $this->json($campus, Response::HTTP_OK, [],['groups' => 'campus-api']);
    }


    #[Route('', name: 'create', methods: ['POST'])]
    public function create(SerializerInterface    $serializer,
                           EntityManagerInterface $entityManager,
                           ValidatorInterface     $validator,
                           Request                $request): Response
    {
        $json = $request->getContent();
        $campus = $serializer->deserialize($json, Campus::class, 'json');
        $errors = $validator->validate($campus);
        if ($errors->count() == 0) {
            $entityManager->persist($campus);
            $entityManager->flush();
            return $this->json($campus, Response::HTTP_CREATED, [],['groups' => 'campus-api']);
        } else {
            return $this->json($errors, Response::HTTP_CREATED, [],['groups' => 'campus-api']);
        }
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $entityManager,
        CampusRepository       $campusRepository,
        int                    $id = null
    ): Response
    {
        $campus = $campusRepository->find($id);

        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->json(['success' => 'Campus supprimée!'], Response::HTTP_ACCEPTED);
    }


    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        Request                $request,
        EntityManagerInterface $entityManager,
        CampusRepository       $campusRepository,
        int                    $id = null
    ): Response
    {
        $campus = $campusRepository->find($id);
        $data = json_decode($request->getContent(), true);

        if (!$campus) {
            return $this->json(['error' => 'Campus non trouvée'], Response::HTTP_NOT_FOUND);
        }
        if (isset($data['nom'])) {
            $campus->setNom($data['nom']);
        }
        $entityManager->flush();
        return $this->json($campus, Response::HTTP_OK, [],['groups' => 'campus-api']);
    }
}
