<?php

namespace App\Command;

use App\Entity\Etat;
use App\Entity\Sortie;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:en-cours',
    description: 'Add a short description for your command',
)]
class EnCoursCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output,


    ): int
    {
        $io = new SymfonyStyle($input, $output);

        $repository = $this->entityManager->getRepository(Sortie::class);
        $etatRepository = $this->entityManager->getRepository(Etat::class);
        $etatEnCours = $etatRepository->findOneBy(['nom' => 'En cours']);
        $etatTermine = $etatRepository->findOneBy(['nom' => 'Terminee']);
        $etatHisto = $etatRepository->findOneBy(['nom' => 'Historisee']);

        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $sortiesOuvertes = $repository->findSortieDemaree($now, $etatRepository->findOneBy(["nom" => "Ouverte"]));
        foreach ($sortiesOuvertes as $sortie) {
            $sortie->setEtat($etatEnCours);
        }

        $sortiesCloturees = $repository->findSortieDemaree($now, $etatRepository->findOneBy(["nom" => 'Cloturee']));
        foreach ($sortiesCloturees as $sortie) {
            $sortie->setEtat($etatEnCours);
        }

        $sortiesEnCours = $repository->findSortieDemaree($now, $etatRepository->findOneBy(["nom" => 'En cours']));
        foreach ($sortiesEnCours as $sortie) {
            $dateLimite = (clone $sortie->getDateHeureDebut())->modify('+'.$sortie->getDuree().' minutes');
            $dateLimite->setTimezone(new \DateTimeZone('Europe/Paris'));
            dump($sortie->getDateHeureDebut(), $dateLimite, $now);
            if($dateLimite <= $now) {
                $sortie->setEtat($etatTermine);
            }}

        $sortiesTerminees = $repository->findSortieDemaree($now, $etatRepository->findOneBy(["nom" => 'Terminee']));
        foreach ($sortiesTerminees as $sortie) {
            $dateLimite = (clone $sortie->getDateHeureDebut())->modify('+1 month');
            $dateLimite->setTimezone(new \DateTimeZone('Europe/Paris'));
            if ($dateLimite <= $now) {
                $sortie->setEtat($etatHisto);
            }
        }


        $this->entityManager->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

}
