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

class PostCheckoutCommand extends Command
{
    const ARG_PREV_COMMIT = 'prevCommit';
    const ARG_POST_COMMIT = 'postCommit';
         
    protected function configure()
    {
        $this
            ->setName('hook:post-checkout')
            ->setDescription('post-checkout hook')
            ->addArgument(self::ARG_PREV_COMMIT)
            ->addArgument(self::ARG_POST_COMMIT);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running post-checkout hook</info>');
        
        $projectPath = realpath(__DIR__ . '/../../');
        $prevCommit = $input->getArgument(self::ARG_PREV_COMMIT);
        $postCommit = $input->getArgument(self::ARG_POST_COMMIT);
        
        chdir($projectPath);
        if (file_exists($projectPath . '/composer.lock')) {
            $gitCommand = "git diff --shortstat $prevCommit..$postCommit composer.lock";
            
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $output->writeln("<info>Executing: $gitCommand</info>");
            }
            
            $process = new Process($gitCommand);
            $process->run();
            $processOutput = $process->getOutput();
            $output->writeln('<error>' . $process->getErrorOutput() . '</error>');
            if ($processOutput != '') {
                $output->writeln("<info>composer.lock has changed. Should run composer install</info>");
                $composerCommand = "composer install -o --prefer-dist --ignore-platform-reqs";
                $process = new Process($composerCommand);
                $process->run();
                $output->writeln('<info>' . $process->getOutput() . '</info>');
                $output->writeln('<error>' . $process->getErrorOutput() . '</error>');
            }
        }
    }
}
