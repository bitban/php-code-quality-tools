<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\GitHooks;

use Bitban\PhpCodeQualityTools\Command\BaseCommand;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\HookManager;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends BaseCommand
{
    const COMMAND_NAME = 'hooks:uninstall';
    const COMMAND_DESCRIPTION = 'Removes Git hooks from destination path';
    const COMMAND_HELP = 'Removes Git hooks from destination path. If backup files exist, restores them.';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hookManager = HookManager::getDefaultInstance($this->projectBasepath)
            ->setOutput($output)
            ->setProgressBar(new ProgressBar($output));

        return $hookManager->uninstallHooks();
    }
}
