<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;
use Bitban\PhpCodeQualityTools\Infrastructure\Project;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class FilesetManipulationCommand extends BaseCommand
{
    const FILE_TYPE_PHP = 'php';
    const FILE_TYPE_JSON = 'json';
    const FILE_TYPE_COMPOSER = 'composer';

    const OPT_EXCLUDED_PATHS = 'excluded-paths';
    const OPT_ONLY_COMMITED_FILES = 'only-commited-files';

    const DEFAULT_EXCLUDED_PATHS = 'bin,tests,vendor';
    const DEFAULT_BITBAN_EXCLUDED_PATHS = 'bin,compiled,frontend,packages,tests,vendor';

    /** @var string[][] */
    private $files = [
        self::FILE_TYPE_PHP => [],
        self::FILE_TYPE_JSON => [],
        self::FILE_TYPE_COMPOSER => []
    ];

    /** @var string[] */
    protected $excludedPaths;

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
     * @return string[]
     */
    protected function getAllFiles()
    {
        return array_unique(array_merge(
            $this->getPhpFiles(),
            $this->getComposerFiles(),
            $this->getJsonFiles()
        ));
    }

    /**
     * @param string $path
     * @param string[] $excludedPaths
     * @return string[]
     */
    private function fetchProjectFiles($path, $excludedPaths)
    {
        return (new Project)->listFiles($path, $excludedPaths);
    }

    /**
     * @param string[] $excludedPaths
     * @param OutputInterface $output
     * @return string[]
     */
    private function fetchCommitedFiles($excludedPaths, $output)
    {
        $output->writeln("<info>Processing commited files</info>");

        $output->write('<info>Fetching changed files...</info>');

        $commitedFiles = (new ExtractCommitedFiles())->setExcludedPaths($excludedPaths)->getFiles();

        $result = (count($commitedFiles) > 1) ? count($commitedFiles) . ' files changed' : 'No files changed';
        $output->writeln("<info>$result</info>");

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $output->writeln("<info>Changed files list</info>");
            foreach ($commitedFiles as $file) {
                $output->writeln($file);
            }
        }

        return $commitedFiles;
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
        parent::configure();
        $this
            ->addOption(self::OPT_ONLY_COMMITED_FILES, null, InputOption::VALUE_NONE, 'If present, only commited files will be processed')
            ->addOption(self::OPT_EXCLUDED_PATHS, null, InputOption::VALUE_OPTIONAL, 'If present, these paths are ignored from processing', self::DEFAULT_BITBAN_EXCLUDED_PATHS);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->excludedPaths = explode(',', $input->getOption(self::OPT_EXCLUDED_PATHS));
        $this->loadFiles($input, $output);
        if (is_file($this->projectBasepath)) {
            // Project basepath may not be a single file. Fallback to default value
            $this->projectBasepath = (new Project())->getBasepath();
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function loadFiles(InputInterface $input, OutputInterface $output)
    {
        $files = ($input->getOption(self::OPT_ONLY_COMMITED_FILES)) ?
            $this->fetchCommitedFiles($this->excludedPaths, $output) :
            $this->fetchProjectFiles($this->projectBasepath, $this->excludedPaths);

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
