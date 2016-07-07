<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Fixers;

use Bitban\PhpCodeQualityTools\Interfaces\FixerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpPsrFixer implements FixerInterface
{
    private $files;
    private $output;

    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;
    }

    /**
     * @param bool $dryRun
     */
    public function fix($dryRun = false)
    {
        $this->output->writeln('<info>Fixing PHP PSR-2 compliance</info>');

        if ($dryRun) {
            $this->output->writeln("<info>Dry run mode, no changes will be made</info>");
        }

        foreach ($this->files as $file) {

            $this->output->writeln('<info>' . ($dryRun ? 'Analysing' : 'Fixing') . ' file ' . $file . '</info>');

            $command = $dryRun ?
                "php bin/phpcs --standard=PSR2 --report-full --report-diff $file" :
                "php bin/phpcbf --standard=PSR2 --extensions=php,inc $file";

            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $this->output->writeln("<info>Running: $command</info>");
            }

            $process = new Process($command);

            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG || $dryRun) {
                $process->setTty(true);
            }
            $process->run();
        }
    }
}
