<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Output\OutputInterface;

class PhpKeywordsValidator extends AbstractValidator
{
    /** @var PhpKeywordValidationRule[] */
    protected $validationRules;

    public function __construct($files, $basePath, OutputInterface $output)
    {
        parent::__construct($files, $basePath, $output);
    }

    protected function getValidatorTitle()
    {
        return 'Validating PHP forbidden keywords';
    }

    /**
     * @param PhpKeywordValidationRule []
     * @return PhpKeywordsValidator
     */
    public function setValidationRules($validationRules)
    {
        $this->validationRules = $validationRules;
        return $this;
    }

    /**
     * @param string $file
     * @param PhpKeywordValidationRule $validationRule
     * @throws ErrorException
     * @throws WarningException
     */
    private function checkForbiddenKeywords($file, $validationRule)
    {
        $sourceCode = file_get_contents($file);
        $tokens = token_get_all($sourceCode);
        array_map(function ($token) use ($validationRule, $file) {
            $validationRule->validateToken($file, $token);
        }, $tokens);
    }

    protected function check($file)
    {
        $warnings = [];
        $errors = [];
        foreach ($this->validationRules as $validation) {
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
