<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\Test;

use Bitban\PhpCodeQualityTools\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends BaseCommand
{
    const COMMAND_NAME = 'test:run';
    const COMMAND_DESCRIPTION = 'Runs lightweight tests of Bitban\'s framework';
    const COMMAND_HELP = 'Runs lightweight tests of Bitban\'s framework. Not useful outside of there.';
    const OPTION_VM_HOST = 'vmHost';

    protected function configure()
    {
        parent::configure();
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addOption(self::OPTION_VM_HOST, null, InputOption::VALUE_OPTIONAL, 'Host name of development VM', 'ndevel');
    }

    /**
     * @param string $vmHost
     * @return bool
     */
    private function checkVmConnection($vmHost)
    {
        if (!$socket = @fsockopen($vmHost, 22)) {
            return false;
        }
        fclose($socket);
        return true;
    }

    /**
     * @param Process $process
     * @param OutputInterface $output
     */
    private function runTests($process, $output)
    {
        if ($output->isDebug()) {
            $process->run(function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->write('<fg=yellow>' . $buffer . '</fg=yellow>');
                } else {
                    $output->write($buffer);
                }
            });
        } else {
            $process->run();
        }
    }

    /**
     * @param Process $process
     * @param OutputInterface $output
     * @return bool
     */
    private function outputTestsResult($process, $output)
    {
        $processExitCode = $process->getExitCode();
        if ($processExitCode > 0) {
            if (mb_strpos($process->getErrorOutput(), 'bash') !== false) {
                $output->writeln('<error>run_light_tests.sh script not found, could not perform any test</error>');
            } else {
                $output->writeln($process->getOutput());
                $output->writeln('<error>Tests FAILED</error>');
            }
        } else {
            $output->writeln('<info>Tests OK</info>');
        }

        return $processExitCode === 0;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vmHost = $input->getOption(self::OPTION_VM_HOST);
        if (!$this->checkVmConnection($vmHost)) {
            $output->writeln("<fg=red>VM host '$vmHost' is down, could not perform any test</fg=red>");
            return false;
        }

        $project = basename($this->projectBasepath);

        $output->writeln("<fg=yellow>Running lightweight tests for project '$project', please be patient...</fg=yellow>");

        $commandLine = <<<CMD
ssh -t root@$vmHost "~/run_light_tests.sh {$project}"
CMD;

        $process = new Process($commandLine);
        $process->setTimeout(3600);

        $this->runTests($process, $output);
        $testsResult = $this->outputTestsResult($process, $output);
        return $testsResult;
    }
}
