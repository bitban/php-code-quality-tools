<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Infrastructure\Git;

class ExtractCommitedFiles
{
    /** @var array */
    private $output = array();
    /** @var int */
    private $rc = 0;

    /** @var string[] */
    private $excludedPaths = [];

    private function execute()
    {
        exec('git rev-parse --verify HEAD 2> /dev/null', $this->output, $this->rc);

        $against = '4b825dc642cb6eb9a060e54bf8d69288fbee4904';
        if ($this->rc === 0) {
            $against = 'HEAD';
        }

        exec("git diff-index --cached --name-status $against | egrep '^(A|M)' | awk '{print $2;}'", $this->output);
    }

    /**
     * @param string[] $excludedPaths
     * @return ExtractCommitedFiles
     */
    public function setExcludedPaths($excludedPaths)
    {
        $this->excludedPaths = $excludedPaths;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getFiles()
    {
        $this->execute();

        $excludedPaths = $this->excludedPaths;
        $files = array_filter($this->output, function($item) use ($excludedPaths) {
            foreach ($excludedPaths as $excludedPath) {
                $excludedPath = rtrim($excludedPath, '/');
                if  (preg_match("#^$excludedPath\/#", $item)) {
                    return false;
                }
            }
            return true; 
        });

        return $files;
    }
}
