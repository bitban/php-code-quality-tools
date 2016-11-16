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

    protected function check($file)
    {
        // First check if file is empty. If so, throw warning (jsonlint would throw error)
        $json = preg_replace('/\s+/', '', file_get_contents($file));
        if (mb_strlen($json) === 0) {
            throw new WarningException(sprintf(Constants::WARNING_MESSAGE_WRAPPER, "$file file is empty"));
        }

        $binPath = (new Project())->getBinPath();
        $process = $this->buildProcess("$binPath/jsonlint $file");

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $process->getErrorOutput()));
        }
    }
}
