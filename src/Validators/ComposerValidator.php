<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

class ComposerValidator extends AbstractValidator
{
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
            throw new ErrorException('composer.lock must be committed if composer.json is modified!');
        }

    }

    protected function check($file)
    {
        // No need to be implemented as validate() has been rewritten
    }

    protected function getValidatorTitle()
    {
        // No need to be implemented as validate() has been rewritten
    }
}
