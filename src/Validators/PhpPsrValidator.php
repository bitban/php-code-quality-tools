<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Interfaces\ValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpPsrValidator implements ValidatorInterface
{
    private $files;
    private $output;

    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;
    }

    /**
     * @throws ErrorException
     */
    public function validate()
    {
        $this->output->writeln('<info>Validating PHP PSR-2 compliance</info>');

        foreach ($this->files as $file) {
            $command = "php bin/phpcs --standard=PSR2 $file";

            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $this->output->writeln("<info>Running: $command</info>");
            }

            $process = new Process($command);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->output->writeln($file);
                $this->output->writeln(sprintf('<error>%s</error>', trim($process->getErrorOutput())));
                
                $processOutput = $process->getOutput();

                $exceptionMessage = "You do not comply PSR-2 in some PHP files.";
                if (preg_match('/ERROR/', $processOutput)) {
                    $exception = new ErrorException($exceptionMessage . ' Fix them before proceeding!');
                    $messageClass = 'error';
                } else {
                    $exception = new WarningException($exceptionMessage);
                    $messageClass = 'comment';
                }
                $this->output->writeln("<$messageClass>$processOutput</$messageClass>");
                throw $exception;
            }
        }
    }
}
