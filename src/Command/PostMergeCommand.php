<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\GitHooks\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PostMergeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('hook:post-merge')
            ->setDescription('post-merge hook');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running post-merge hook</info>');

        $projectPath = realpath(__DIR__ . '/../../');

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('<info>Project path: ' . $projectPath . '</info>');
        }

        chdir($projectPath);
        $gitCommand = "git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD | grep composer.lock > /dev/null";
        
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $output->writeln('<info>Executing: ' . $gitCommand . '</info>');
        }
        
        $process = new Process($gitCommand);
        $process->run();
        $processOutput = $process->getOutput();
        $output->writeln('<error>' . $process->getErrorOutput() . '</error>');
        if ($processOutput != '') {
            $output->writeln('<error>composer.lock has changed. Should run composer install<error>');
            $composerCommand = "composer install -o --prefer-dist --ignore-platform-reqs";
            $process = new Process($composerCommand);
            $process->run();
            $output->writeln('<info>' . $process->getOutput() . '</info>');
            $output->writeln('<error>' . $process->getErrorOutput() . '</error>');
        }
    }
}
