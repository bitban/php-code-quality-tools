<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\CodeStyle;


use Bitban\PhpCodeQualityTools\Command\GitHooks\PreCommitCommand;
use Bitban\PhpCodeQualityTools\Fixers\PhpPsrFixer;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FixCodeStyleCommand extends Command
{
    const COMMAND_NAME = 'codestyle:fix';
    const COMMAND_DESCRIPTION = 'Fixes PHP code style according to PSR-2 rules';
    const COMMAND_HELP = 'Fixes code style of files according to PSR-2 recommendations. It may fix all project files or only files to be commited.';
    const ARG_PROJECT_PATH = 'projectPath';
    const OPT_COMMITED_FILES = 'commited-files';

    const PHP_FILES_IN_SRC = '/^(.*)(\.php)|(\.inc)$/';
    const JSON_FILES_IN_SRC = '/^(.*)(\.json)$/';
    const COMPOSER_FILES = '/^composer\.(json|lock)$/';

    /** @var  array */
    private $changedFiles = [
        'php' => [],
        'json' => [],
        'composer' => []
    ];
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addArgument(self::ARG_PROJECT_PATH, InputArgument::REQUIRED)
            ->addOption(self::OPT_COMMITED_FILES, null, InputOption::VALUE_NONE, 'If present, only commited files will be fixed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::OPT_COMMITED_FILES)) {
            $output->writeln("<info>Fixing commited files</info>");
            $this->extractCommitFiles($output);
            $files = $this->changedFiles['php'];
        } else {
            $output->writeln("<info>Fixing project files</info>");
            $files = [$input->getArgument(self::ARG_PROJECT_PATH)];
        }
        (new PhpPsrFixer($files, $output))->fix();
    }

    /**
     * @param OutputInterface $output
     */
    private function extractCommitFiles($output)
    {
        $output->write('<info>Fetching changed files...</info>');
        $commitFiles = new ExtractCommitedFiles();
        $changedFiles = $commitFiles->getFiles();

        foreach ($changedFiles as $file) {
            if (preg_match(PreCommitCommand::PHP_FILES_IN_SRC, $file)) {
                $this->changedFiles['php'][] = $file;
            }
            if (preg_match(PreCommitCommand::COMPOSER_FILES, $file)) {
                $this->changedFiles['composer'][] = $file;
            }
            if (preg_match(PreCommitCommand::JSON_FILES_IN_SRC, $file)) {
                $this->changedFiles['json'][] = $file;
            }
        }

        $result = (count($changedFiles) > 1) ? count($changedFiles) . ' files changed' : 'No files changed';
        $output->writeln("<info>$result</info>");

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $output->writeln("<info>Changed files list</info>");
            foreach ($this->changedFiles as $type) {
                foreach ($type as $file) {
                    $output->writeln($file);
                }
            }
        }
    }
}
