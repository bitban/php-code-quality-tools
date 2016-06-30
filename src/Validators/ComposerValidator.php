<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Interfaces\ValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerValidator implements ValidatorInterface
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
        $this->output->writeln('<info>Validating composer files</info>');
        
        $composerJsonDetected = false;
        $composerLockDetected = false;

        foreach ($this->files as $file) {
            if ($file === 'composer.json') {
                $composerJsonDetected = true;
            }

            if ($file === 'composer.lock') {
                $composerLockDetected = true;
            }
        }

        if ($composerJsonDetected && !$composerLockDetected) {
            throw new ErrorException("composer.lock must be committed if composer.json is modified!");
        }
    }
}
