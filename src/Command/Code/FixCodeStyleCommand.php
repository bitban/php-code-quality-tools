<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\Code;

use Bitban\PhpCodeQualityTools\Command\FilesetManipulationCommand;
use Bitban\PhpCodeQualityTools\Fixers\PhpCodeStyleFixer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FixCodeStyleCommand extends FilesetManipulationCommand
{
    const COMMAND_NAME = 'code:fix-codestyle';
    const COMMAND_DESCRIPTION = 'Fixes PHP code style according to Bitban\'s code style';
    const COMMAND_HELP = 'Fixes code style of files according to Bitban\'s code style recommendations. It may fix all project files or only files to be commited.';
    const OPT_DRY_RUN = 'dry-run';

    protected function configure()
    {
        parent::configure();
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addOption(self::OPT_DRY_RUN, null, InputOption::VALUE_NONE, 'If present, it shows diffs but does not change any files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        (new PhpCodeStyleFixer($this->getPhpFiles(), $output))
            ->fix($input->getOption(self::OPT_DRY_RUN), $input->getOption(self::OPT_ONLY_COMMITED_FILES));
    }
}
