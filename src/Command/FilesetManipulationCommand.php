<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

abstract class FilesetManipulationCommand extends Command
{
    const FILE_TYPE_PHP = 'php';
    const FILE_TYPE_JSON = 'json';
    const FILE_TYPE_COMPOSER = 'composer';

    const ARG_PATH = 'projectPath';
    const OPT_ONLY_COMMITED_FILES = 'only-commited-files';
    
    /** @var string[][] */
    private $files = [
        self::FILE_TYPE_PHP => [],
        self::FILE_TYPE_JSON => [],
        self::FILE_TYPE_COMPOSER => []
    ];

    /**
     * @param string $file
     */
    protected function addPhpFile($file)
    {
        $this->files[self::FILE_TYPE_PHP][] = $file;
    }

    /**
     * @param string $file
     */
    protected function addJsonFile($file)
    {
        $this->files[self::FILE_TYPE_JSON][] = $file;
    }

    /**
     * @param string $file
     */
    protected function addComposerFile($file)
    {
        $this->files[self::FILE_TYPE_COMPOSER][] = $file;
    }

    /**
     * @return string[]
     */
    protected function getPhpFiles()
    {
        return $this->files[self::FILE_TYPE_PHP];
    }

    /**
     * @return string[]
     */
    protected function getJsonFiles()
    {
        return $this->files[self::FILE_TYPE_JSON];
    }

    /**
     * @return string[]
     */
    protected function getComposerFiles()
    {
        return $this->files[self::FILE_TYPE_COMPOSER];
    }

    /**
     * @param string $path
     * @param array $excluded
     * @param OutputInterface $output
     * @return \string[]
     */
    protected function listFiles($path, $excluded, $output)
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
    protected function extractCommitFiles($output)
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
    protected function isProcessingAnyComposerFile()
    {
        return count($this->files[self::FILE_TYPE_COMPOSER]) > 0;
    }

    /**
     * @return bool
     */
    protected function isProcessingAnyPhpFile()
    {
        return count($this->files[self::FILE_TYPE_PHP]) > 0;
    }

    /**
     * @return bool
     */
    protected function isProcessingAnyJsonFile()
    {
        return count($this->files[self::FILE_TYPE_JSON]) > 0;
    }

    protected function configure()
    {
        $this
            ->addArgument(self::ARG_PATH, InputArgument::REQUIRED)
            ->addOption(self::OPT_ONLY_COMMITED_FILES, null, InputOption::VALUE_NONE, 'If present, only commited files will be processed');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadFiles($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function loadFiles(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::OPT_ONLY_COMMITED_FILES)) {
            $files = $this->extractCommitFiles($output);
        } else {
            $excluded = $input->getOption(self::OPT_ONLY_COMMITED_FILES) ? ['bin', 'vendor', 'tests'] : ['vendor', 'bin'];
            $files = $this->listFiles($input->getArgument(self::ARG_PATH), $excluded, $output);
        }

        foreach ($files as $file) {
            if (preg_match(Constants::PHP_FILES_REGEXP, basename($file))) {
                $this->addPhpFile($file);
            }
            if (preg_match(Constants::COMPOSER_FILES_REGEXP, basename($file))) {
                $this->addComposerFile($file);
            }
            if (preg_match(Constants::JSON_FILES_REGEXP, basename($file))) {
                $this->addJsonFile($file);
            }
        }
    }
}