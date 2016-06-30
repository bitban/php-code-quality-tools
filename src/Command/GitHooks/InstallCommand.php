<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\GitHooks;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InstallCommand extends Command
{
    const COMMAND_NAME = 'hooks:install';
    const COMMAND_DESCRIPTION = 'Installs Git hooks into destination path';
    const COMMAND_HELP = 'Installs Git hooks into destination path. If destination path already exists, it is deleted and recreated.';
    const ARG_SOURCE_PATH = 'hooksSourcePath';
    const ARG_DESTINATION_PATH = 'hooksDestinationPath';
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addArgument(self::ARG_SOURCE_PATH, InputArgument::REQUIRED)
            ->addArgument(self::ARG_DESTINATION_PATH, InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourcePath = $input->getArgument(self::ARG_SOURCE_PATH);
        $destinationPath = $input->getArgument(self::ARG_DESTINATION_PATH);

        try {
            $filesystem = new Filesystem();

            $this->recursiveRemoveDirectory($destinationPath);
            $filesystem->mkdir($destinationPath);

            $hooks = [];
            foreach (new \DirectoryIterator($sourcePath) as $hook) {
                if ($hook->isDot()) {
                    continue;
                }
                $hooks[] = $hook->getFilename();
            }

            $progressBar = new ProgressBar($output, count($hooks));
            $progressBar->setBarCharacter(Constants::CHARACTER_PROGRESS_BAR);
            $progressBar->setProgressCharacter(Constants::CHARACTER_BEER);
            
            foreach ($hooks as $hook) {
                $sourceFile = $sourcePath . '/' . $hook;
                $destinationFile = $destinationPath . '/' . $hook;

                $filesystem->copy($sourceFile, $destinationFile, true);
                $filesystem->chmod($destinationFile, 0755);
                $progressBar->advance();

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                    $output->writeln(" <info>Copying $sourceFile to $destinationFile</info>");
                }
            }
            
            $progressBar->finish();
            $output->writeln('');
            $output->writeln(sprintf('<info>Hooks installed succesfully %s</info>', Constants::CHARACTER_THUMB_UP));
        } catch (\Exception $e) {
            $output->writeln(" <error>" . $e->getMessage() . " Aborting</error>");
            return 1;
        }

        return 0;
    }

    private function recursiveRemoveDirectory($directory)
    {
        if (is_link($directory)) {
            unlink($directory);
        }

        foreach (glob("{$directory}/*") as $file) {
            if (is_dir($file)) {
                $this->recursiveRemoveDirectory($file);
            } else {
                unlink($file);
            }
        }

        if (is_dir($directory)) {
            rmdir($directory);
        }
    }
}
