<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Project;

class JsonValidator extends AbstractValidator
{
    protected function getValidatorTitle()
    {
        return 'Validating JSON syntax';
    }

    public static function getDisabledText()
    {
        return 'JSON syntax validation disabled';
    }

    protected function check($file)
    {
        $binPath = (new Project())->getBinPath();
        $process = $this->buildProcess("$binPath/jsonlint $file");
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $process->getErrorOutput()));
        }
    }
}
