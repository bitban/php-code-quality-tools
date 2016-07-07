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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command
{
    const COMMAND_NAME = 'hooks:check';
    const COMMAND_DESCRIPTION = 'Checks if Git hooks are installed';
    const COMMAND_HELP = 'Checks if Git hooks are installed. If not, it gives a hint to install them, but does not take any action automatically';
    const ARG_GIT_PROJECT_PATH = 'gitProjectPath';
    const OPTION_SKIP_OK_MESSAGE = 'skip-ok';
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addArgument(self::ARG_GIT_PROJECT_PATH, InputArgument::OPTIONAL, 'Project path (current directory by default)', getcwd())
            ->addOption(self::OPTION_SKIP_OK_MESSAGE, '', InputOption::VALUE_NONE, 'Do not show OK message');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectPath = $input->getArgument(self::ARG_GIT_PROJECT_PATH);
        $sourcePath = __DIR__ . '/../../../hooks';
        $destinationPath = $projectPath . '/.git/hooks';
        
        $realProjectPath = realpath($projectPath);
        $realSourcePath = realpath($sourcePath);
        $realDestinationPath = realpath($destinationPath);

        if (!$realProjectPath) {
            throw new \Exception("Cannot find project in $projectPath");
        }
        if (!$realSourcePath) {
            throw new \Exception("Cannot find hooks in $sourcePath");
        }
        if (!$realDestinationPath) {
            throw new \Exception("Cannot find hooks installation path: $destinationPath");
        }

        $result = true;
        try {
            foreach (new \DirectoryIterator($realSourcePath) as $hook) {
                if ($hook->isDot()) {
                    continue;
                }

                $sourceFile = $realSourcePath . '/' . $hook;
                $destinationFile = $realDestinationPath . '/' . $hook;

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
            $output->writeln("<error>Your hooks are not properly configured!</error>\n");
            $output->writeln("<comment>You may install them running the folowing command:\n\n$realProjectPath/bin/php-cqtools $installCommand $realSourcePath $realDestinationPath\n</comment>");
            $result = false;
        } else {
            if (!$input->getOption(self::OPTION_SKIP_OK_MESSAGE)) {
                $output->writeln("<info>Your hooks are properly set. Nice job!</info> " . Constants::CHARACTER_THUMB_UP);
            }
        }

        return $result ? 0 : 1;
    }
}
