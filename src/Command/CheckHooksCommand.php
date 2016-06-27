<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\GitHooks\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckHooksCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('hook:check-hooks')
            ->setDescription('Check if hooks are installed')
            ->addArgument('projectPath')
            ->addArgument('libraryPath');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectPath = $input->getArgument('projectPath');
        $libraryPath = $input->getArgument('libraryPath');
        
        $gitHooksPath = $projectPath . '/.git/hooks';
        $libraryHooksPath = $libraryPath . '/hooks';

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln("<info>Project path: $projectPath</info>");
            $output->writeln("<info>Git hooks path: $gitHooksPath</info>");
            $output->writeln("<info>Library hooks path: $libraryHooksPath</info>");
        }

        $result = true;
        try {
            foreach (new \DirectoryIterator($gitHooksPath) as $file) {
                if($file->isDot()) continue;
                
                $gitHook = $gitHooksPath . '/' . $file;
                $libraryHook = $libraryHooksPath . '/' . $file;
                
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                    $output->writeln("<info>Comparing $gitHook with $libraryHook</info>");
                }
                
                $result = $result && (@file_get_contents($gitHook) === @file_get_contents($libraryHook));
            }
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            $result = false;
        }

        $output->setDecorated(true);
        if (!$result) {
            $output->writeln("<error>Hey! Do not cheat and set up your hooks!</error>\xF0\x9F\x91\x8E");
            $output->writeln("<info>Please run following commands:\ncd $projectPath\nrm -rf .git/hooks\nln -s ../vendor/bitban/git-hooks/hooks .git/hooks</info>");
            $result = false;
        } else {
            $output->writeln("<info>Hooks installed. Well done! \xF0\x9F\x91\x8D");
        }

        return $result;
    }
}
