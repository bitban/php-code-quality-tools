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

class PostMergeCommand extends Command
{
    use CommonActionsTrait;
    
    const COMMAND_NAME = 'hooks:post-merge';
    const COMMAND_DESCRIPTION = 'post-merge Git hook';
    const ARG_PROJECT_PATH = 'projectPath';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->addArgument(self::ARG_PROJECT_PATH, InputArgument::OPTIONAL, 'Project base path', GitHelper::getProjectBasepath());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running post-merge hook</info>');

        $projectPath = realpath($input->getArgument(self::ARG_PROJECT_PATH));

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('<info>Project path: ' . $projectPath . '</info>');
        }

        $gitCommand = "git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD | grep composer.lock";
        $this->checkComposerLockChanges($projectPath, $gitCommand, $output);
    }
}
