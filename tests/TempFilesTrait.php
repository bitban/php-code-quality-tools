<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

trait TempFilesTrait
{
    private $tmpdir;

    public function setUp()
    {
        $this->tmpdir = rtrim(sys_get_temp_dir(), '/') . '/' . uniqid();
        exec("mkdir -p $this->tmpdir");
    }

    private function writeTempFiles($files)
    {
        foreach ($files as $filename => $filecontent) {
            file_put_contents($this->tmpdir . '/' . $filename, $filecontent);
        }
        return array_keys($files);
    }

    private function cleanTempFiles()
    {
        exec("rm -f $this->tmpdir/*");
    }

    public function tearDown()
    {
        exec("rm -rf $this->tmpdir");
    }
}