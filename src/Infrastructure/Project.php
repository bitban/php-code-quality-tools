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
    private $basepath = null;

    /**
     * Project constructor.
     * @param string $basepath @optional
     */
    public function __construct($basepath = null)
    {
        $this->basepath = $basepath === null ? GitHelper::getProjectBasepath() : $basepath;
    }

    public function getBasepath()
    {
        return $this->basepath;
    }

    public function getBinPath()
    {
        return $this->getBasepath() . '/bin';
    }

    /**
     * Returns a list of PHP, JSON and Composer files (according to regexp detection) with full path for each file.
     * Obviously, excluded paths are discarded
     *
     * @param string $path May be directory or file
     * @param string[] $excludedPaths
     * @return string[]
     */
    public function listFiles($path, $excludedPaths = [])
    {
        $realpath = realpath($path);
        $finder = new Finder();
        $finder
            ->files()
            ->exclude($excludedPaths);

        if (is_file($realpath)) {
            $finder
                ->in(dirname($realpath))
                ->name(basename($realpath));
        } else {
            $finder
                ->in($realpath)
                ->name(Constants::PHP_FILES_REGEXP)
                ->name(Constants::JSON_FILES_REGEXP)
                ->name(Constants::COMPOSER_FILES_REGEXP);
        }

        $filenames = [];
        foreach ($finder as $file) {
            $filenames[] = $file->getRealPath();
        }
        return $filenames;
    }
}
