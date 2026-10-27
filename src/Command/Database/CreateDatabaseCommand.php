<?php

declare(strict_types=1);

namespace App\Command\Database;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'amadougafar:db:create',
    description: 'Supprime la base de données si elle existe, puis la recrée.'
)]
class CreateDatabaseCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private readonly string $environment,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Forcer la suppression et la recréation de la base de données');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->environment === 'prod') {
            $io->error('Cette commande ne peut pas être exécutée en environnement de production.');

            return Command::FAILURE;
        }

        $force = (bool) $input->getOption('force');

        if (!$force) {
            if (!$input->isInteractive()) {
                $io->error('L\'option --force est requise en mode non interactif. Utilisez --force pour exécuter cette commande.');

                return Command::FAILURE;
            }

            if (!$io->confirm('Êtes-vous sûr de vouloir supprimer et recréer la base de données ?', false)) {
                $io->warning('Opération annulée : rien n\'a été modifié.');

                return Command::SUCCESS;
            }
        }

        $dropExitCode = $this->runChildCommand(
            'doctrine:database:drop',
            [
                '--force' => true,
                '--if-exists' => true,
            ],
            $output
        );

        if ($dropExitCode !== Command::SUCCESS) {
            $io->error('Échec de la suppression de la base de données : rien n\'a été modifié. Veuillez vérifier qu\'aucune connexion active (pgAdmin, serveur web, autre terminal) n\'utilise la base de données.');

            return Command::FAILURE;
        }

        $createExitCode = $this->runChildCommand(
            'doctrine:database:create',
            [],
            $output
        );

        if ($createExitCode !== Command::SUCCESS) {
            $io->error('L\'ancienne base de données a été supprimée mais la nouvelle n\'a pas pu être créée.');

            return Command::FAILURE;
        }

        $io->success('La base de données a été supprimée et recréée avec succès.');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function runChildCommand(string $commandName, array $parameters, OutputInterface $output): int
    {
        try {
            $application = $this->getApplication();
            if ($application === null) {
                return Command::FAILURE;
            }

            $command = $application->find($commandName);
            $input = new ArrayInput($parameters);
            $input->setInteractive(false);

            return $command->run($input, $output);
        } catch (\Throwable) {
            return Command::FAILURE;
        }
    }
}
