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
    const COMMAND_NAME = 'post-merge';
    const COMMAND_DESCRIPTION = 'post-merge Git hook';
    const ARG_PROJECT_PATH = 'projectPath';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->addArgument(self::ARG_PROJECT_PATH);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running post-merge hook</info>');

        $projectPath = realpath($input->getArgument(self::ARG_PROJECT_PATH));

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('<info>Project path: ' . $projectPath . '</info>');
        }

        chdir($projectPath);
        $gitCommand = "git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD | grep composer.lock";
        
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $output->writeln('<info>Executing: ' . $gitCommand . '</info>');
        }
        
        $process = new Process($gitCommand);
        $process->run();
        $processOutput = $process->getOutput();
        $output->writeln('<error>' . $process->getErrorOutput() . '</error>');
        if ($processOutput != '') {
            $output->writeln('<info>composer.lock has changed. Should run composer install<info>');
            $composerCommand = "composer install -o --prefer-dist --ignore-platform-reqs";
            $process = new Process($composerCommand);
            $process->run();
            $output->writeln('<info>' . $process->getOutput() . '</info>');
            $output->writeln('<info>' . $process->getErrorOutput() . '</info>');
        }
    }
}
