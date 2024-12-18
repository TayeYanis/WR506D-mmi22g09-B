<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Repository\ActorRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:details-entity',
    description: 'Liste les détails des entités : films, acteurs, et catégories.',
)]
class DetailsEntityCommand extends Command
{
    private MovieRepository $movieRepository;
    private ActorRepository $actorRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(
        MovieRepository $movieRepository,
        ActorRepository $actorRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->movieRepository = $movieRepository;
        $this->actorRepository = $actorRepository;
        $this->categoryRepository = $categoryRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Affiche les résultats sans effectuer de traitement réel (mode test).'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si le mode dry-run est activé
        $dryRun = $input->getOption('dry-run');
        if ($dryRun) {
            $io->note('Mode Dry-Run activé : Aucune donnée ne sera modifiée.');
        }

        // 1. Nombre total de films
        $movieCount = $this->movieRepository->count([]);
        $io->section('Statistiques des Films');
        $io->writeln(sprintf('Nombre total de films : %d', $movieCount));

        // 2. Nombre total d'acteurs
        $actorCount = $this->actorRepository->count([]);
        $io->section('Statistiques des Acteurs');
        $io->writeln(sprintf('Nombre total d\'acteurs : %d', $actorCount));

        // 3. Nombre total de catégories
        $categoryCount = $this->categoryRepository->count([]);
        $io->section('Statistiques des Catégories');
        $io->writeln(sprintf('Nombre total de catégories : %d', $categoryCount));

        // 4. Nombre de films par catégorie
        $io->section('Nombre de Films par Catégorie');
        $categories = $this->categoryRepository->findAll();
        foreach ($categories as $category) {
            $moviesInCategory = $this->movieRepository->count(['category' => $category]);
            $io->writeln(sprintf(
                'Catégorie : %s | Nombre de films : %d',
                $category->getName(),
                $moviesInCategory
            ));
        }

        // Si c'est en mode dry-run, ne pas effectuer d'action de sauvegarde ou autre
        if ($dryRun) {
            $io->success('Mode Dry-Run : Aucune action n\'a été effectuée.');
        } else {
            $io->success('Statistiques calculées avec succès.');
        }

        return Command::SUCCESS;
    }
}