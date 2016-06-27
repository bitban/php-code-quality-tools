<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\GitHooks\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InstallCommand extends Command
{
    const COMMAND_NAME = 'install';
    const COMMAND_DESCRIPTION = 'Install Git hooks';
    const ARG_SOURCE_PATH = 'sourcePath';
    const ARG_DESTINATION_PATH = 'destinationPath';
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
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
            $progressBar->setBarCharacter('<fg=green>=</>');
            $progressBar->setProgressCharacter("\xF0\x9F\x8D\xBA");
            
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
            $output->writeln("<info>Hooks installed succesfully \xF0\x9F\x91\x8D</info>");
            
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

        foreach (glob("{$directory}/*") as $file)
        {
            if (is_dir($file)) {
                $this->recursiveRemoveDirectory($file);
            } else {
                unlink($file);
            }
        }

        rmdir($directory);
    }
}
