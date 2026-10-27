<?php

declare(strict_types=1);

namespace App\Command\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'amadougafar:make:entity',
    description: 'Crée ou met à jour une entité Doctrine.'
)]
class MakeEntityCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Le nom de la classe d\'entité à créer ou mettre à jour');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $application = $this->getApplication();
        if ($application === null || !$application->has('make:entity')) {
            $io->error('MakerBundle est indisponible. Cette commande dépend de symfony/maker-bundle qui n\'est disponible qu\'en environnement de développement.');

            return Command::FAILURE;
        }

        $name = $input->getArgument('name');
        $options = [];
        if ($name !== null && $name !== '') {
            $options['name'] = (string) $name;
        }

        $exitCode = $this->runChildCommand('make:entity', $options, $output, $input->isInteractive());

        if ($exitCode !== Command::SUCCESS) {
            $io->error('Une erreur est survenue lors de l\'exécution de make:entity.');

            return Command::FAILURE;
        }

        $io->success('L\'entité a été traitée avec succès.');

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
