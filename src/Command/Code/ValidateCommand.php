<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\Code;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;
use Bitban\PhpCodeQualityTools\Validators\ComposerValidator;
use Bitban\PhpCodeQualityTools\Validators\JsonValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpForbiddenKeywordsValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpPsrValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpSniffsValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpSyntaxValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ValidateCommand extends Command
{
    const COMMAND_NAME = 'code:validate';
    const COMMAND_DESCRIPTION = 'Performs all code validations across files in given path';
    const COMMAND_HELP = 'Performs all code validations across files in given path.';
    const ARG_PATH = 'projectPath';
    const OPT_ONLY_COMMITED_FILES = 'only-commited-files';

    /** @var string[][] */
    private $filesToValidate = [
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
            ->addArgument(self::ARG_PATH, InputArgument::REQUIRED)
            ->addOption(self::OPT_ONLY_COMMITED_FILES, null, InputOption::VALUE_NONE, 'If present, only commited files will be validated');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::OPT_ONLY_COMMITED_FILES)) {
            $files = $this->extractCommitFiles($output);
        } else {
            $excluded = $input->getOption(self::OPT_ONLY_COMMITED_FILES) ? ['bin', 'vendor', 'tests'] : ['vendor', 'bin'];
            $files = $this->listFiles($input->getArgument(self::ARG_PATH), $excluded, $output);
        }
        
        if (count($files) === 0) {
            // No files to be processed
            return 0;
        }

        foreach ($files as $file) {
            if (preg_match(Constants::PHP_FILES_REGEXP, basename($file))) {
                $this->filesToValidate['php'][] = $file;
            }
            if (preg_match(Constants::COMPOSER_FILES_REGEXP, basename($file))) {
                $this->filesToValidate['composer'][] = $file;
            }
            if (preg_match(Constants::JSON_FILES_REGEXP, basename($file))) {
                $this->filesToValidate['json'][] = $file;
            }
        }

        $returnCode = Constants::RETURN_CODE_OK;

        $validators = [];

        if (true === $this->isProcessingAnyComposerFile()) {
            if ($input->getOption(self::OPT_ONLY_COMMITED_FILES)) {
                $validators[] = new ComposerValidator($this->filesToValidate['composer'], $output);
            }
        }

        if (true === $this->isProcessingAnyPhpFile()) {
            $validators[] = new PhpSyntaxValidator($this->filesToValidate['php'], $output);
            $validators[] = new PhpForbiddenKeywordsValidator($this->filesToValidate['php'], $output);
            $validators[] = new PhpPsrValidator($this->filesToValidate['php'], $output);
            $validators[] = new PhpSniffsValidator($this->filesToValidate['php'], $output);
        }

        if (true === $this->isProcessingAnyJsonFile()) {
            $validators[] = new JsonValidator($this->filesToValidate['json'], $output);
        }

        foreach ($validators as $validator) {
            $returnCode = max($returnCode, $validator->validate());
        }

        if ($returnCode > 0) {
            $output->writeln('Return code: ' . $returnCode);
        }

        return $returnCode;
    }

    /**
     * @param string $path
     * @param array $excluded
     * @param OutputInterface $output
     * @return \string[]
     */
    private function listFiles($path, $excluded, $output)
    {
        // Single file is also accepted as "path"
        if (is_file($path)) {
            $output->writeln("<info>Validating $path</info>");
            return [$path];
        }

        // Remove trailing slash if present
        $path = rtrim($path, '/');

        $output->writeln("<info>Validating files in $path</info>");
        $finder = new Finder();
        $finder
            ->files()
            ->in($path)
            ->name(Constants::PHP_FILES_REGEXP)
            ->name(Constants::JSON_FILES_REGEXP)
            ->name(Constants::COMPOSER_FILES_REGEXP)
            ->exclude($excluded);
        return iterator_to_array($finder);
    }

    /**
     * @param OutputInterface $output
     * @return string[]
     */
    private function extractCommitFiles($output)
    {
        $output->writeln("<info>Validating commited files</info>");
        $output->write('<info>Fetching changed files...</info>');
        $commitFiles = new ExtractCommitedFiles();
        $changedFiles = $commitFiles->getFiles();

        $result = (count($changedFiles) > 1) ? count($changedFiles) . ' files changed' : 'No files changed';
        $output->writeln("<info>$result</info>");

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $output->writeln("<info>Changed files list</info>");
            foreach ($changedFiles as $file) {
                $output->writeln($file);
            }
        }

        return $changedFiles;
    }

    /**
     * @return bool
     */
    private function isProcessingAnyComposerFile()
    {
        return count($this->filesToValidate['composer']) > 0;
    }

    /**
     * @return bool
     */
    private function isProcessingAnyPhpFile()
    {
        return count($this->filesToValidate['php']) > 0;
    }

    /**
     * @return bool
     */
    private function isProcessingAnyJsonFile()
    {
        return count($this->filesToValidate['json']) > 0;
    }
}
