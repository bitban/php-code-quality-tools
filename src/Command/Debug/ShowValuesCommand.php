<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\Debug;

use Bitban\PhpCodeQualityTools\Command\FilesetManipulationCommand;
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
    const OPTION_SHOW_FULL_PATH = 'show-full-path';

    protected function configure()
    {
        parent::configure();
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addOption(self::OPTION_SHOW_FULL_PATH, null, InputOption::VALUE_NONE, 'If present, full paths will be shown');
    }

    /**
     * @param string[] $files
     * @param bool $showFullPath
     * @return string[]
     */
    private function prefixFiles($files, $showFullPath)
    {
        $prefix = $showFullPath ? $this->projectBasepath . '/' : '';
        return array_map(function($item) use ($prefix) {
            return $prefix . $item;
        }, $files);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $showFullPath = $input->getOption(self::OPTION_SHOW_FULL_PATH);

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
        $output->writeln('<info>Commited files' . (!$showFullPath ? ' (from project basepath)' : '') . '</info>:');
        foreach ($this->prefixFiles($commitedFiles, $showFullPath) as $file) {
            $output->writeln($file);
        }
        // Excluded paths
        $output->writeln('<info>Excluded paths' . (!$showFullPath ? ' (from project basepath)' : '') . '</info>:');
        foreach ($this->prefixFiles($this->excludedPaths, $showFullPath) as $path) {
            $output->writeln($path);
        }
        // Project files (not in excluded paths)
        $projectFiles = $this->getAllFiles();
        $output->writeln('<info>Project files' . (!$showFullPath ? ' (from project basepath)' : '') . '</info>:');
        foreach ($this->prefixFiles($projectFiles, $showFullPath) as $file) {
            $output->writeln($file);
        }
    }
}
