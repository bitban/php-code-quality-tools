<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\GitHooks\Validators;


use Bitban\GitHooks\Interfaces\ValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpCodeValidator implements ValidatorInterface
{
    private $files;
    private $output;
    
    private $validations;

    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;
        
        $this->validations = [
            ['code' => 'var_dump', 'message' => 'var_dump() call found', 'severity' => ValidatorInterface::SEVERITY_ERROR],
            ['code' => 'empty', 'message' => 'empty operator found', 'severity' => ValidatorInterface::SEVERITY_WARNING],
        ];
    }

    /**
     * @throws ErrorException
     * @throws WarningException
     */
    public function validate()
    {
        $this->output->writeln('<info>Validating PHP code</info>');

        foreach ($this->files as $file) {

            foreach ($this->validations as $validation) {
                $process = new Process(sprintf('grep --fixed-strings --ignore-case --quiet "%s" %s', $validation['code'], $file));
                $process->run();

                if ($process->isSuccessful()) {
                    $severity = $validation['severity'] === ValidatorInterface::SEVERITY_WARNING ? 'fg=yellow' : 'fg=red';
                    $message = '[' . $validation['severity'] . '] ' . $validation['message'];
                    $this->output->writeln($file);
                    $this->output->writeln("<$severity>$message in file $file</$severity>");

                    $exceptionMessage = "You have not allowed code in PHP files.";
                    if ($validation['severity'] === ValidatorInterface::SEVERITY_WARNING) {
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
