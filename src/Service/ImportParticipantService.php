<?php
namespace App\Service;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportParticipantService
{

//injection de dependances
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ParticipantRepository $participantRepository,
    ) {}

    public function importFromCsv(File $csvFile): array
    {
//        lecture du csv
        $csvContent = file_get_contents($csvFile->getPathname());
        $reader = Reader::fromString($csvContent);
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);

//      compteurs pour le nb de participant et le nb d'erreur (doublon)
        $created = $errors = 0;
//        tableau tampon car flush tous les 50 entités
        $batch = [];
//        mdp par defaut il va etre hashé
        $defaultPassword = '123456';
// $row est un tableau associatif
        foreach ($reader as $row) {
//            saute la ligne si pseudo vide
            if (empty($row['pseudo'])) continue;
//          verifie les  doublon
            if ($this->participantRepository->findOneBy(['email' => $row['mail']]) ||
                $this->participantRepository->findOneBy(['username' => $row['pseudo']])) {
//          si il y a  deja des utilisateur deja creer avec le meme mail et le meme pseudo on incremente error et on saute la ligne
                $errors++;
                continue;
            }
//          création de l'entité
            $participant = new Participant();
            $participant->setUsername($row['pseudo']);
            $participant->setNom($row['nom']);
            $participant->setPrenom($row['prenom']);
            $participant->setEmail($row['mail']);
            $participant->setTel($row['telephone'] ?? null);
            $participant->setRoles(['ROLE_USER']);
            $participant->setActif(true);
//            hashage du mot de passe
            $participant->setPassword($this->passwordHasher->hashPassword($participant, $defaultPassword));

            $this->entityManager->persist($participant);
            $batch[] = $participant;

            if (count($batch) >= 50) {
                $this->entityManager->flush();
                $batch = [];
            }
//            compte le nombre de creation réaliseer
            $created++;
        }
//        flush finale pour les moins de 50 entité
        $this->entityManager->flush();
//      on renvoit un tableaux avec le nb de création, d'erreur et le mdp par deffaut
        return ['created' => $created, 'errors' => $errors, 'password' => $defaultPassword];
    }
}
