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
            ['code' => 'var_dump', 'message' => 'var_dump() call found', 'severity' => Constants::RETURN_CODE_ERROR],
            ['code' => 'empty', 'message' => 'empty operator found', 'severity' => Constants::RETURN_CODE_WARNING]
        ];
    }

    protected function getValidatorTitle()
    {
        return 'Validating PHP forbidden keywords';
    }
    
    private function checkForbiddenKeywords($file, $validation)
    {
        $process = $this->buildProcess(sprintf('grep --fixed-strings --ignore-case -n "%s" %s', $validation['code'], $file));
        
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
