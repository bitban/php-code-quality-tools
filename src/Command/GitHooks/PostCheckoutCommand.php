<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\GitHooks;

use Bitban\PhpCodeQualityTools\Infrastructure\Git\GitHelper;
use Bitban\PhpCodeQualityTools\Traits\CommonActionsTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostCheckoutCommand extends Command
{
    use CommonActionsTrait;
    
    const COMMAND_NAME = 'hooks:post-checkout';
    const COMMAN_DESCRIPTION = 'post-checkout Git hook';
    const ARG_PROJECT_PATH = 'projectPath';
    const ARG_PREV_COMMIT = 'prevCommit';
    const ARG_POST_COMMIT = 'postCommit';
         
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAN_DESCRIPTION)
            ->addArgument(self::ARG_PREV_COMMIT, InputArgument::REQUIRED)
            ->addArgument(self::ARG_POST_COMMIT, InputArgument::REQUIRED)
            ->addArgument(self::ARG_PROJECT_PATH, InputArgument::OPTIONAL, 'Project base path', GitHelper::getProjectBasepath());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running post-checkout hook</info>');
        
        $projectPath = realpath($input->getArgument(self::ARG_PROJECT_PATH));
        $prevCommit = $input->getArgument(self::ARG_PREV_COMMIT);
        $postCommit = $input->getArgument(self::ARG_POST_COMMIT);

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('<info>Project path: ' . $projectPath . '</info>');
        }

        $gitCommand = "git diff --shortstat $prevCommit..$postCommit composer.lock";
        $this->checkComposerLockChanges($projectPath, $gitCommand, $output);
    }
}
