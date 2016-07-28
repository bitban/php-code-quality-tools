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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command
{
    const COMMAND_NAME = 'hooks:check';
    const COMMAND_DESCRIPTION = 'Checks if Git hooks are installed';
    const COMMAND_HELP = 'Checks if Git hooks are installed. If not, it gives a hint to install them, but does not take any action automatically';
    const OPTION_SKIP_OK_MESSAGE = 'skip-ok';
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addOption(self::OPTION_SKIP_OK_MESSAGE, '', InputOption::VALUE_NONE, 'Do not show OK message');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hookManager = HookManager::getDefaultInstance()
            ->setOutput($output)
            ->setProgressBar(new ProgressBar($output));

        return $hookManager->checkHooks($input->getOption(self::OPTION_SKIP_OK_MESSAGE));
    }
}
