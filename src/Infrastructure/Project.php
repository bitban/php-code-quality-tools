<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Infrastructure;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\GitHelper;
use Symfony\Component\Finder\Finder;

class Project
{
    public function getBasepath()
    {
        return GitHelper::getProjectBasepath();
    }

    /**
     * @param string $path
     * @param string[] $excludedPaths
     * @return string[]
     */
    public function listFiles($path, $excludedPaths = [])
    {
        if (is_file($path)) {
            $defaultProjectBasepath = (new Project())->getBasepath();
            $file = str_replace($defaultProjectBasepath . '/', '', $path);
            return [$file];
        }

        $finder = new Finder();
        $finder
            ->files()
            ->in($path)
            ->name(Constants::PHP_FILES_REGEXP)
            ->name(Constants::JSON_FILES_REGEXP)
            ->name(Constants::COMPOSER_FILES_REGEXP)
            ->exclude($excludedPaths);

        $filenames = [];
        foreach ($finder as $file) {
            $fileRealname = $file->getRealPath();
            $filenames[] = str_replace($path . '/', '', $fileRealname);
        }
        return $filenames;
    }
}
