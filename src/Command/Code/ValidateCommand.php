<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\Code;


use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;
use Bitban\PhpCodeQualityTools\Validators\ErrorException;
use Bitban\PhpCodeQualityTools\Validators\PhpCodeValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpPsrValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpSniffsValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpSyntaxValidator;
use Bitban\PhpCodeQualityTools\Validators\WarningException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends Command
{
    const COMMAND_NAME = 'code:validate';
    const COMMAND_DESCRIPTION = 'Performs all code validations across files in given path';
    const COMMAND_HELP = 'Performs all code validations across files in given path.';
    const ARG_PATH = 'projectPath';
    const OPT_COMMITED_FILES = 'commited-files';

    const PHP_FILES_IN_SRC = '/^(.*)(\.php)|(\.inc)$/';

    /** @var array */
    private $changedFiles;

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addArgument(self::ARG_PATH, InputArgument::REQUIRED)
            ->addOption(self::OPT_COMMITED_FILES, null, InputOption::VALUE_NONE, 'If present, only commited files will be validated');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::OPT_COMMITED_FILES)) {
            $output->writeln("<info>Validating commited files</info>");
            $this->extractCommitFiles($output);
        } else {
            $output->writeln("<info>Validating project files</info>");
            $this->changedFiles = [$input->getArgument(self::ARG_PATH)];
        }

        try {
            $this->extractCommitFiles($output);

            (new PhpSyntaxValidator($this->changedFiles, $output))->validate();
            (new PhpCodeValidator($this->changedFiles, $output))->validate();
            (new PhpPsrValidator($this->changedFiles, $output))->validate();
            (new PhpSniffsValidator($this->changedFiles, $output))->validate();

            return 0;
        } catch (WarningException $we) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $output->writeln('<info>' . $we->getMessage() . '</info>');
            }
            return 2;
        } catch (ErrorException $ee) {
            $output->writeln('<error>' . $ee->getMessage() . '</error>');
            return 1;
        }
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
            if (preg_match(ValidateCommand::PHP_FILES_IN_SRC, $file)) {
                $this->changedFiles[] = $file;
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
