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

        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $SortieRepository = $this->entityManager->getRepository(Sortie::class);
        $etatRepository = $this->entityManager->getRepository(Etat::class);

        $etats = $etatRepository->findEtat();
        $etatSortieAModifier = $SortieRepository->findSortieEtatAModifier($now, $now, $etats);


        foreach ($etatSortieAModifier as $sortie) {

            $dateLimite = (clone $sortie->getDateHeureDebut())->modify('+' . $sortie->getDuree() . ' minutes');
            $dateLimite->setTimezone(new \DateTimeZone('Europe/Paris'));

            $dateLimiteHisto = (clone $sortie->getDateHeureDebut())->modify('+1 month');
            $dateLimiteHisto->setTimezone(new \DateTimeZone('Europe/Paris'));

            $dateLimiteIns = (clone $sortie->getDateLimiteInscription());
            $dateLimiteIns->setTimezone(new \DateTimeZone('Europe/Paris'));

            if ($dateLimiteIns <= $now && $sortie->getEtat()->getnom() == 'Ouverte') {
                $sortie->setEtat($etats[1]);
            }

            if ($dateLimite <= $now) {
                if (
                    $sortie->getEtat()->getnom() == 'Ouverte' || $sortie->getEtat()->getnom() == 'Cloturee'
                ) {
                    $sortie->setEtat($etats[2]);
                }
                if ($sortie->getEtat()->getnom() == 'En cours'
                ) {
                    $sortie->setEtat($etats[3]);
                }
            }

            if ($dateLimiteHisto <= $now &&
                (
                    $sortie->getEtat()->getnom() == 'Terminee' || $sortie->getEtat()->getnom() == 'Annulee')
            ) {
                $sortie->setEtat($etats[4]);
            }
        }

        $this->entityManager->flush();
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        return Command::SUCCESS;
    }

}
