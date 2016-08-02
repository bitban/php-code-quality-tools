<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Fixers;

use Bitban\PhpCodeQualityTools\Interfaces\FixerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpCodeStyleFixer implements FixerInterface
{
    private $files;
    private $output;

    private $ruleset;

    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;
        $this->ruleset = realpath(__DIR__ . '/../../rulesets/bitban.xml');
    }

    /**
     * @param string $ruleset
     * @return PhpCodeStyleFixer
     * @throws \Exception
     */
    public function setRuleset($ruleset)
    {
        if (realpath($ruleset)) {
            $this->ruleset = realpath($ruleset);
        } else {
            throw new \Exception("Custom ruleset $ruleset not found");
        }

        return $this;
    }

    /**
     * @param bool $dryRun
     * @param bool $gitReAdd
     */
    public function fix($dryRun = false, $gitReAdd = false)
    {
        $this->output->writeln('<info>Fixing PHP code style compliance</info>');

        if ($dryRun) {
            $this->output->writeln("<info>Dry run mode, no changes will be made</info>");
        }

        foreach ($this->files as $file) {

            $this->output->writeln('<info>' . ($dryRun ? 'Analysing' : 'Fixing') . ' file ' . $file . '</info>');

            $command = $dryRun ?
                "php bin/phpcs --standard=$this->ruleset --report-full --report-diff $file" :
                "php bin/phpcbf --standard=$this->ruleset --extensions=php,inc $file";

            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $this->output->writeln("<info>Running: $command</info>");
            }

            $process = new Process($command);

            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG || $dryRun) {
                $process->setTty(true);
            }
            $process->run();

            if ($gitReAdd) {
                (new Process("git add $file"))->run();
            }
        }
    }
}
