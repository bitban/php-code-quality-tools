<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Process\Process;

class PhpSyntaxValidator extends AbstractValidator
{
    protected function getValidatorTitle()
    {
        return 'Validating PHP syntax';
    }

    protected function check($file)
    {
        $process = new Process("php -l $file");
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $process->getErrorOutput()));
        }
    }
}
