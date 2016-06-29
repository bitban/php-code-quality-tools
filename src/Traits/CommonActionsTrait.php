<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Traits;


use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

trait CommonActionsTrait
{
    /**
     * @param OutputInterface $output
     */
    protected function composerInstall(OutputInterface $output)
    {
        $output->writeln("<info>composer.lock has changed. Running composer install</info>");
        $composerCommand = "composer install -o --prefer-dist --ignore-platform-reqs";

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $output->writeln("<info>Executing: $composerCommand</info>");
        }

        $process = new Process($composerCommand);
        $process->run();
        $output->writeln($process->getOutput());
        $output->writeln($process->getErrorOutput());
    }

    /**
     * @param string $projectPath
     * @param string $gitCommand
     * @param OutputInterface $output
     */
    protected function checkComposerLockChanges($projectPath, $gitCommand, OutputInterface $output)
    {
        chdir($projectPath);
        if (file_exists($projectPath . '/composer.lock')) {

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $output->writeln("<info>Executing: $gitCommand</info>");
            }

            $process = new Process($gitCommand);
            $process->run();
            $processOutput = $process->getOutput();
            $output->writeln('<error>' . $process->getErrorOutput() . '</error>');
            if ($processOutput != '') {
                $this->composerInstall($output);
            }
        }
    }
}
