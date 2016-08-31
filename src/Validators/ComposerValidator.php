<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;

class ComposerValidator extends AbstractValidator
{
    private function projectIsLibrary()
    {
        $composer = json_decode(file_get_contents($this->basePath . '/composer.json'), true);
        return $composer['type'] === 'library';
    }

    /**
     * @return int Result Code = Constants::RETURN_CODE_OK|Constants::RETURN_CODE_ERROR
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

        if ($composerJsonDetected && !$composerLockDetected && !$this->projectIsLibrary()) {
            $this->output->writeln(sprintf(Constants::ERROR_MESSAGE_WRAPPER, 'composer.lock must be committed if composer.json is modified!'));
            return Constants::RETURN_CODE_ERROR;
        }
        
        return Constants::RETURN_CODE_OK;
    }

    public static function getDisabledText()
    {
        return 'Composer files validation disabled';
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
