<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;


use Bitban\PhpCodeQualityTools\Interfaces\ValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpSniffsValidator implements ValidatorInterface
{
    private $files;
    private $output;

    private $sniffs;

    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;

        $this->sniffs = [
            'Generic.CodeAnalysis.VariableAnalysis',
        ];
    }

    /**
     * @throws ErrorException
     * @throws WarningException
     */
    public function validate()
    {
        $this->output->writeln('<info>Validating PHP sniffsº</info>');

        foreach ($this->files as $file) {
            foreach ($this->sniffs as $sniff) {
                $process = new Process(sprintf('php bin/phpcs --standard=Generic --sniffs=%s %s', $sniff, $file));
                $process->run();

                $processOutput = $process->getOutput();
                if ($processOutput != '') {
                    $severity = preg_match('/\bERROR\b/', $processOutput) ? ValidatorInterface::SEVERITY_ERROR : ValidatorInterface::SEVERITY_WARNING;
                    $messageClass = $severity === ValidatorInterface::SEVERITY_ERROR ? 'fg=red' : 'fg=yellow';
                    $this->output->writeln("<$messageClass>$file</$messageClass>");
                    $this->output->writeln("<$messageClass>$processOutput</$messageClass>");

                    $exceptionMessage = "You have not allowed code in PHP files.";
                    if ($severity === ValidatorInterface::SEVERITY_WARNING) {
                        $exception = new WarningException($exceptionMessage);
                    } else {
                        $exception = new ErrorException($exceptionMessage . ' Fix it before proceeding!');
                    }
                    throw $exception;
                }
            }
        }
    }
}
