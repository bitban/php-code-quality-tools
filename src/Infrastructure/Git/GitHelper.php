<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Infrastructure\Git;


class GitHelper
{
    public static function getProjectBasepath()
    {
        $selfDir = __DIR__;
        return exec("cd $selfDir; git rev-parse --show-toplevel");
    }

    public static function getHooksPath()
    {
        return GitHelper::getProjectBasepath() . '/.git/hooks';
    }

    public static function getHooksSourcePath()
    {
        return realpath(__DIR__ . '/../../../hooks');
    }
}
