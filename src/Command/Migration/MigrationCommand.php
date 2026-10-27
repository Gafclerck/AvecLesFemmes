<?php

declare(strict_types=1);

namespace App\Command\Migration;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'amadougafar:migration',
    description: 'Génère la migration puis l\'exécute.'
)]
class MigrationCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $application = $this->getApplication();
        if ($application === null || !$application->has('make:migration')) {
            $io->error('MakerBundle est indisponible. Cette commande dépend de symfony/maker-bundle qui n\'est disponible qu\'en environnement de développement.');

            return Command::FAILURE;
        }

        $makeMigrationExitCode = $this->runChildCommand('make:migration', [], $output, false);
        if ($makeMigrationExitCode !== Command::SUCCESS) {
            $io->warning('Aucune migration n\'a été générée (rien à générer ou migrations en attente).');
        }

        $migrateExitCode = $this->runChildCommand(
            'doctrine:migrations:migrate',
            ['--allow-no-migration' => true],
            $output,
            false
        );

        if ($migrateExitCode !== Command::SUCCESS) {
            $io->error('Une erreur est survenue lors de l\'exécution des migrations.');

            return Command::FAILURE;
        }

        $io->success('Les migrations ont été traitées avec succès.');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, mixed> $options
     */
    private function runChildCommand(string $name, array $options, OutputInterface $output, bool $interactive): int
    {
        try {
            $application = $this->getApplication();
            if ($application === null) {
                return Command::FAILURE;
            }

            $command = $application->find($name);
            $input = new ArrayInput($options);
            $input->setInteractive($interactive);

            return $command->run($input, $output);
        } catch (\Throwable) {
            return Command::FAILURE;
        }
    }
}
