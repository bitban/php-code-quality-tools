<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\Debug;

use Bitban\PhpCodeQualityTools\Command\FilesetManipulationCommand;
use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\GitHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowValuesCommand extends FilesetManipulationCommand
{
    const COMMAND_NAME = 'debug:show';
    const COMMAND_DESCRIPTION = 'Shows values calculated by tool';
    const COMMAND_HELP = 'Shows values calculated by tool. Useful for debugging purposes, may disappear in the future.';
    const OPTION_SHOW_FULL_PATHS = 'show-full-paths';

    protected function configure()
    {
        parent::configure();
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addOption(self::OPTION_SHOW_FULL_PATHS, null, InputOption::VALUE_NONE, 'If present, files will be listed with full paths');
    }

    /**
     * @param string[] $files
     * @param bool $showFullPath
     * @return string[]
     */
    private function prefixFiles($files, $showFullPath)
    {
        $prefix = $showFullPath ? $this->projectBasepath . '/' : '';
        return array_map(function ($item) use ($prefix) {
            return $prefix . $item;
        }, $files);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $showFullPath = $input->getOption(self::OPTION_SHOW_FULL_PATHS);

        // Available tools
        $tools = ['jsonlint', 'phpcs', 'phpcbf'];
        $output->writeln('<info>Available tools</info>:');
        $missingTools = false;
        foreach ($tools as $tool) {
            if (!file_exists($this->projectBasepath . '/bin/' . $tool)) {
                $output->writeln($tool . ' ' . Constants::CHARACTER_KO);
                $missingTools = true;
            } else {
                $output->writeln($tool . ' ' . Constants::CHARACTER_OK);
            }
        }
        if ($missingTools) {
            $output->writeln('<fg=red>Some tools are missing. Did you forget to execute composer install?</fg=red>');
        }

        // Project basepath
        $output->writeln('<info>Project basepath</info>:');
        $output->writeln($this->projectBasepath);
        // Hooks path
        $hooksPath = GitHelper::getHooksPath();
        $output->writeln('<info>Hooks path</info>:');
        $output->writeln($hooksPath);
        // Hooks source path
        $hooksSourcePath = GitHelper::getHooksSourcePath();
        $output->writeln('<info>Hooks source path</info>:');
        $output->writeln($hooksSourcePath);
        // Commited files
        $commitedFiles = (new ExtractCommitedFiles())->getFiles();
        $output->writeln('<info>Commited files</info>:');
        foreach ($this->prefixFiles($commitedFiles, $showFullPath) as $file) {
            $output->writeln($file);
        }
        // Excluded paths
        $output->writeln('<info>Excluded paths</info>:');
        foreach ($this->prefixFiles($this->excludedPaths, $showFullPath) as $path) {
            $output->writeln($path);
        }
        // Project files (not in excluded paths)
        $projectFiles = $this->getAllFiles();
        $output->writeln('<info>Project files</info>:');
        foreach ($this->prefixFiles($projectFiles, $showFullPath) as $file) {
            $output->writeln($file);
        }
    }
}
