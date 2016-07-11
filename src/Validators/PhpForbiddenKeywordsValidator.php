<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Output\OutputInterface;

class PhpForbiddenKeywordsValidator extends AbstractValidator
{
    protected $forbiddenKeywords;

    public function __construct($files, OutputInterface $output)
    {
        parent::__construct($files, $output);
        
        $this->forbiddenKeywords = [
            ['code' => '^[^//].*\bvar_dump\s*\(', 'message' => 'var_dump() function call found', 'severity' => Constants::RETURN_CODE_ERROR],
            ['code' => '^[^//].*\bempty\s*\(', 'message' => 'empty() operator found', 'severity' => Constants::RETURN_CODE_WARNING]
        ];
    }

    protected function getValidatorTitle()
    {
        return 'Validating PHP forbidden keywords';
    }
    
    private function checkForbiddenKeywords($file, $validation)
    {
        $command = sprintf('egrep -n "%s" %s', $validation['code'], $file);
        $process = $this->buildProcess($command);

        $process->run();
        
        if ($process->isSuccessful()) {
            $message = $file . ': ' . $validation['message'] . "\n" . $process->getOutput();
            if ($validation['severity'] === Constants::RETURN_CODE_WARNING) {
                $exception = new WarningException(sprintf(Constants::WARNING_MESSAGE_WRAPPER, $message));
            } else {
                $exception = new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $message));
            }
            throw $exception;
        }
    }

    protected function check($file)
    {
        $warnings = [];
        $errors = [];
        foreach ($this->forbiddenKeywords as $validation) {
            try {
                $this->checkForbiddenKeywords($file, $validation);
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
