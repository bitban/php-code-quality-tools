<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\GitHelper;
use Symfony\Component\Process\Process;

class PhpSniffsValidator extends AbstractValidator
{
    protected function getValidatorTitle()
    {
        return 'Validating PHP sniffs';
    }

    protected function check($file)
    {
        $projectBasepath = GitHelper::getProjectBasepath();
        $customStandard = realpath(__DIR__ . '/../Infrastructure/CodeSniffer/Standards/Custom');
        $process = new Process("$projectBasepath/bin/phpcs --standard=$customStandard $file");

        $process->run();

        $processOutput = $process->getOutput();
        if ($processOutput != '') {
            $severity = preg_match('/\bERROR\b/', $processOutput) ? Constants::RETURN_CODE_ERROR : Constants::RETURN_CODE_WARNING;
            if ($severity === Constants::RETURN_CODE_WARNING) {
                $exception = new WarningException(sprintf(Constants::WARNING_MESSAGE_WRAPPER, $processOutput));
            } else {
                $exception = new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $processOutput));
            }
            throw $exception;
        }
    }
}
