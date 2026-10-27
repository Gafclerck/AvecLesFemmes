<?php

declare(strict_types=1);

namespace App\Command\Cache;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'amadougafar:cache:clear',
    description: 'Vide le cache de l\'application.'
)]
class ClearCacheCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $exitCode = $this->runChildCommand('cache:clear', [], $output);

        if ($exitCode !== Command::SUCCESS) {
            $io->error('Échec du vidage du cache de l\'application.');

            return Command::FAILURE;
        }

        $io->success('Le cache de l\'application a été vidé avec succès.');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, mixed> $options
     */
    private function runChildCommand(string $name, array $options, OutputInterface $output): int
    {
        try {
            $application = $this->getApplication();
            if ($application === null) {
                return Command::FAILURE;
            }

            $command = $application->find($name);
            $input = new ArrayInput($options);
            $input->setInteractive(false);

            return $command->run($input, $output);
        } catch (\Throwable) {
            return Command::FAILURE;
        }
    }
}
