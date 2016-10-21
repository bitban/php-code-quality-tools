<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Output\OutputInterface;

class PhpMultibyteStringFunctionsValidator extends PhpForbiddenKeywordsValidator
{
    public function __construct($files, $basePath, OutputInterface $output)
    {
        parent::__construct($files, $basePath, $output);

        $this->forbiddenKeywords = [
            ['code' => '(ereg|eregi|eregi_replace|ereg_replace|mail|split|stripos|stristr|strlen|strpos|strrchr|strripos|strrpos|strstr|strtolower|strtoupper|substr|substr_count)\s*\(', 'message' => 'Not multibyte string function found. Are you sure?', 'severity' => Constants::RETURN_CODE_WARNING]
        ];
    }
}
