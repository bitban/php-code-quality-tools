<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Interfaces\ValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpSniffsValidator extends AbstractValidator
{
    protected $sniffs;

    public function __construct($files, OutputInterface $output)
    {
        parent::__construct($files, $output);

        $this->sniffs = [
            'Generic.CodeAnalysis.VariableAnalysis',
        ];
    }

    protected function getValidatorTitle()
    {
        return 'Validating PHP sniffs';
    }

    private function checkSniff($file, $sniff)
    {
        $process = new Process(sprintf('php bin/phpcs --standard=Generic --sniffs=%s %s', $sniff, $file));

        $process->run();

        $processOutput = $process->getOutput();
        if ($processOutput != '') {
            $severity = preg_match('/\bERROR\b/', $processOutput) ? ValidatorInterface::SEVERITY_ERROR : ValidatorInterface::SEVERITY_WARNING;
            if ($severity === ValidatorInterface::SEVERITY_WARNING) {
                $exception = new WarningException(sprintf(Constants::WARNING_MESSAGE_WRAPPER, $processOutput));
            } else {
                $exception = new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $processOutput));
            }
            throw $exception;
        }
    }

    protected function check($file)
    {
        $warnings = [];
        $errors = [];
        foreach ($this->sniffs as $sniff) {
            try {
                $this->checkSniff($file, $sniff);
            } catch (WarningException $we) {
                $warnings[] = $we->getMessage();
            } catch (ErrorException $ee) {
                $errors[] = $ee->getMessage();
            }
        }
        if (count($errors) > 0) {
            throw new ErrorException(join("\n", array_merge($errors, $warnings)));
        } elseif (count($warnings) > 0) {
            throw new WarningException(join("\n", $warnings));
        }
    }
}
