<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools;

class Configuration
{
    const GROUP_VALIDATIONS = 'validations';

    const KEY_PHP_SYNTAX = 'php_syntax';
    const KEY_FORBIDDEN_KEYWORDS = 'forbidden_keywords';
    const KEY_VARIABLE_USAGE = 'variable_usage';
    const KEY_CODESTYLE = 'codestyle';
    const KEY_JSON_SYNTAX = 'json_syntax';
    const KEY_COMPOSER_FILES_COMMITED_TOGETHER = 'composer_files_commited_together';
    const KEY_CODESTYLE_RULESET = 'codestyle_ruleset';

    private $configuration;

    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    private function checkGroupKeyExists($group, $key)
    {
        return
            array_key_exists($group, $this->configuration) &&
            array_key_exists($key, $this->configuration[$group]);
    }

    private function getKey($key)
    {
        if (!array_key_exists($key, $this->configuration)) {
            throw new \Exception("Invalid configuration key $key");
        }
        return $this->configuration[$key];
    }

    private function getValidationKey($key)
    {
        $group = self::GROUP_VALIDATIONS;
        if (!$this->checkGroupKeyExists($group, $key)) {
            throw new \Exception("Invalid configuration key $group/$key");
        }
        return $this->configuration[$group][$key];
    }

    private function isValidationKeyTrue($key)
    {
        return $this->getValidationKey($key) === true;
    }

    public function validatePhpSyntax()
    {
        return $this->isValidationKeyTrue(self::KEY_PHP_SYNTAX);
    }

    public function validateForbiddenKeywords()
    {
        return $this->isValidationKeyTrue(self::KEY_FORBIDDEN_KEYWORDS);
    }

    public function validateVariableUsage()
    {
        return $this->isValidationKeyTrue(self::KEY_VARIABLE_USAGE);
    }

    public function validateCodestyle()
    {
        return $this->isValidationKeyTrue(self::KEY_CODESTYLE);
    }

    public function validateJsonSyntax()
    {
        return $this->isValidationKeyTrue(self::KEY_JSON_SYNTAX);
    }

    public function validateComposerFilesCommitedTogether()
    {
        return $this->isValidationKeyTrue(self::KEY_COMPOSER_FILES_COMMITED_TOGETHER);
    }

    public function getCodestyleRuleset()
    {
        return $this->getKey(self::KEY_CODESTYLE_RULESET);
    }
}
