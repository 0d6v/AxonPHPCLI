<?php

declare(strict_types=1);

namespace Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ci:init',
    description: 'Erstellt CI Konfiguration'
)]
final class CiInitCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Erstellt CI Konfiguration')
            ->addArgument(
                'provider',
                InputArgument::REQUIRED,
                'github'
            )
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $provider = $input->getArgument('provider');

        if ('github' !== $provider) {
            $output->writeln(
                '<error>Nur github wird aktuell unterstützt.</error>'
            );

            return Command::FAILURE;
        }

        $templatePath
            = dirname(__DIR__)
            .'/templates/github/test.yml';

        $template = file_get_contents($templatePath);

        if (false === $template) {
            $output->writeln(
                '<error>Template nicht gefunden.</error>'
            );

            return Command::FAILURE;
        }

        $workflowDirectory
            = getcwd()
            .DIRECTORY_SEPARATOR
            .'.github'
            .DIRECTORY_SEPARATOR
            .'workflows';

        if (!is_dir($workflowDirectory)) {
            mkdir($workflowDirectory, 0o777, true);
        }

        $targetFile
            = $workflowDirectory
            .DIRECTORY_SEPARATOR
            .'test.yml';

        file_put_contents(
            $targetFile,
            $template
        );

        $output->writeln(
            '<info>Workflow erstellt:</info>'
        );

        $output->writeln($targetFile);

        return Command::SUCCESS;
    }
}
