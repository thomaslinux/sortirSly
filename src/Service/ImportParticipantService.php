<?php
namespace App\Service;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportParticipantService
{


    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ParticipantRepository $participantRepository,
        private ManagerRegistry $doctrine
    ) {}

    public function importFromCsv(File $csvFile): array
    {
//        lecture du csv
        $csvContent = file_get_contents($csvFile->getPathname());
        $reader = Reader::fromString($csvContent);
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);


        $participantRepository = $this->doctrine->getRepository(Participant::class);

        $created = $errors = 0;
        $batch = [];
//        mdp par defaut
        $defaultPassword = '123456';

        foreach ($reader as $row) {
            if (empty($row['pseudo'])) continue;

            if ($this->participantRepository->findOneBy(['email' => $row['mail']]) ||
                $this->participantRepository->findOneBy(['username' => $row['pseudo']])) {
                $errors++;
                continue;
            }

            $participant = new Participant();
            $participant->setUsername($row['pseudo']);
            $participant->setNom($row['nom']);
            $participant->setPrenom($row['prenom']);
            $participant->setEmail($row['mail']);
            $participant->setTel($row['telephone'] ?? null);
            $participant->setRoles(['ROLE_USER']);
            $participant->setActif(true);
            $participant->setPassword($this->passwordHasher->hashPassword($participant, $defaultPassword));

            $this->entityManager->persist($participant);
            $batch[] = $participant;

            if (count($batch) >= 50) {
                $this->entityManager->flush();
                $batch = [];
            }
            $created++;
        }
        $this->entityManager->flush();

        return ['created' => $created, 'errors' => $errors, 'password' => $defaultPassword];
    }
}
