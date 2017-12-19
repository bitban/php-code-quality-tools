<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Fixers;

use Bitban\PhpCodeQualityTools\Interfaces\FixerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpCustomFixer implements FixerInterface
{
    private $files;
    private $output;

    private $fixers = [
        'visibility' => [
            'errorString' => 'Visibility must be declared on method',
            'patch' => ['pattern' => '/^(\s*)(.*function.+)$/', 'replacement' => '$1public $2']
        ],
        'var' => [
            'errorString' => 'The var keyword must not be used to declare a',
            'patch' => ['pattern' => '/var\s+/', 'replacement' => 'public ']
        ]
    ];

    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;
    }

    /**
     * @param $fixers
     * @param $file
     * @return string
     */
    private function applyFixers($fixers, $file)
    {
        $sourceFile = file($file);
        foreach ($fixers as $fixerKey => $lines) {
            $fixer = $this->fixers[$fixerKey];
            foreach ($lines as $line) {
                $sourceFile[$line] = preg_replace($fixer['patch']['pattern'], $fixer['patch']['replacement'], $sourceFile[$line]);
            }
        }
        return join('', $sourceFile);
    }

    private function getFixers($file)
    {
        $ruleset = realpath(__DIR__ . '/../../rulesets/bitban.xml');

        $fixers = [];
        foreach ($this->fixers as $fixerKey => $fixer) {
            $errorString = $fixer['errorString'];
            $command = "php bin/phpcs --standard=$ruleset --report-full --report-diff $file | grep '$errorString' | awk '{ print $1 }'";
            $process = new Process($command);
            $process->run();
            if ($process->getOutput() == '') {
                continue;
            }
            $linesWithErrors = array_map(function ($item) {
                return $item - 1;
            }, preg_split('/\R/', $process->getOutput()));
            $linesWithErrors = array_filter($linesWithErrors, function ($item) {
                return $item >= 0;
            });
            if (count($linesWithErrors) > 0) {
                $fixers[$fixerKey] = $linesWithErrors;
            }
        }
        return $fixers;
    }

    public function fix($dryRun = false)
    {
        $this->output->writeln('<info>Fixing custom</info>');

        if ($dryRun) {
            $this->output->writeln("<info>Dry run mode, no changes will be made</info>");
        }

        foreach ($this->files as $file) {
            $fixers = $this->getFixers($file);
            if (count($fixers) === 0) {
                continue;
            }

            $this->output->writeln('<info>' . ($dryRun ? 'Analysing' : 'Fixing') . ' file ' . $file . '</info>');
            $fixedFile = $this->applyFixers($fixers, $file);

            if ($dryRun) {
                // Show diffs
                $tmpdir = rtrim(sys_get_temp_dir(), '/') . '/' . uniqid();
                (new Process("mkdir -p $tmpdir"))->run();
                file_put_contents($tmpdir . '/original', file_get_contents($file));
                file_put_contents($tmpdir . '/fixed', $fixedFile);
                $command = "diff $tmpdir/original $tmpdir/fixed";
                $process = new Process($command);
                $process->run();
                echo $process->getOutput();
                (new Process("rm -rf $tmpdir"))->run();
            } else {
                file_put_contents($file, $fixedFile);
            }
        }
    }
}
