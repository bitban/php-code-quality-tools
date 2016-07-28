<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\GitHooks;

use Bitban\PhpCodeQualityTools\Infrastructure\Git\HookManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    const COMMAND_NAME = 'hooks:install';
    const COMMAND_DESCRIPTION = 'Installs Git hooks into .git/hooks/';
    const COMMAND_HELP = 'Installs Git hooks into .git/hooks/. If destination files already exist, the are backed up.';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hookManager = new HookManager($output, new ProgressBar($output));
        $hookManager->installHooks(HookManager::getHooksPath());
    }
}
