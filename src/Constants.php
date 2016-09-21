<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools;

class Constants
{
    const CHARACTER_OK = "<fg=green>\xE2\x9C\x93</fg=green>";
    const CHARACTER_KO = "<fg=red>\xE2\x9C\x97</fg=red>";
    const CHARACTER_THUMB_UP = "\xF0\x9F\x91\x8D";
    
    const CHARACTER_PROGRESS_BAR = '<fg=green>=</fg=green>';
    const CHARACTER_BEER = "\xF0\x9F\x8D\xBA";

    const WARNING_MESSAGE_WRAPPER = '<bg=yellow;fg=black>%s</bg=yellow;fg=black>';
    const ERROR_MESSAGE_WRAPPER = '<bg=red>%s</bg=red>';
    
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_WARNING = 1;
    const RETURN_CODE_ERROR = 2;

    const PHP_FILES_REGEXP = '/^(.*)\.(php|inc)$/';
    const JSON_FILES_REGEXP = '/^(.*)\.json$/';
    const COMPOSER_FILES_REGEXP = '/^composer\.(json|lock)$/';
}
