<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;

class JsonValidator extends AbstractValidator
{
    protected function getValidatorTitle()
    {
        return 'Validating JSON syntax';
    }

    protected function check($file)
    {
        $process = $this->buildProcess("bin/jsonlint $file");
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $process->getErrorOutput()));
        }
    }
}
