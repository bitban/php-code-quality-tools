<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\GitHooks;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command
{
    const COMMAND_NAME = 'hooks:check';
    const COMMAND_DESCRIPTION = 'Checks if Git hooks are installed';
    const COMMAND_HELP = 'Checks if Git hooks are installed. If not, it gives a hint to install them, but does not take any action automatically';
    const ARG_SOURCE_PATH = 'hooksSourcePath';
    const ARG_DESTINATION_PATH = 'hooksDestinationPath';
    const ARG_PROJECT_PATH = 'projectPath';
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addArgument(self::ARG_SOURCE_PATH, InputArgument::REQUIRED)
            ->addArgument(self::ARG_DESTINATION_PATH, InputArgument::REQUIRED)
            ->addArgument(self::ARG_PROJECT_PATH, InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourcePath = $input->getArgument(self::ARG_SOURCE_PATH);
        $destinationPath = $input->getArgument(self::ARG_DESTINATION_PATH);
        $projectPath = realpath($input->getArgument(self::ARG_PROJECT_PATH));
        
        $result = true;
        try {
            foreach (new \DirectoryIterator($sourcePath) as $hook) {
                if($hook->isDot()) continue;

                $sourceFile = $sourcePath . '/' . $hook;
                $destinationFile = $destinationPath . '/' . $hook;

                $filesMatch = @file_get_contents($sourceFile) === @file_get_contents($destinationFile);

                $result = $result && $filesMatch;

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                    $output->writeln("<info>Comparing $sourceFile with $destinationFile</info> " . 
                        ($filesMatch ? Constants::CHARACTER_OK : Constants::CHARACTER_KO));
                }
            }
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            $result = false;
        }

        $output->setDecorated(true);
        if (!$result) {
            $installCommand = InstallCommand::COMMAND_NAME;
            $output->writeln("<error>Your hooks are not properly configured!</error>");
            $output->writeln("<comment>You may install them running the folowing command:\n\n$projectPath/bin/php-cqtools $installCommand $sourcePath $destinationPath\n</comment>");
            $result = false;
        }

        return $result ? 0 : 1;
    }
}
