<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\GitHooks;

use Bitban\PhpCodeQualityTools\Command\BaseCommand;
use Bitban\PhpCodeQualityTools\Traits\CommonActionsTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostMergeCommand extends BaseCommand
{
    use CommonActionsTrait;
    
    const COMMAND_NAME = 'hooks:post-merge';
    const COMMAND_DESCRIPTION = 'post-merge Git hook';
    const ARG_PROJECT_PATH = 'projectPath';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running post-merge hook</info>');

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('<info>Project path: ' . $this->projectBasepath . '</info>');
        }

        $gitCommand = "git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD | grep composer.lock";
        $this->checkComposerLockChanges($this->projectBasepath, $gitCommand, $output);
    }
}
