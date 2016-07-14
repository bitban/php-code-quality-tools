<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;

class ExtractCommitedFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractCommitedFiles()
    {
        $tmpdir = sys_get_temp_dir() . uniqid();

        $script = <<<COMMAND
mkdir -p $tmpdir/dir1
mkdir -p $tmpdir/dir2
mkdir -p $tmpdir/dir3
cd $tmpdir
git init
touch $tmpdir/dir1/file1
touch $tmpdir/dir1/file2
touch $tmpdir/dir2/file3
touch $tmpdir/dir2/file4
touch $tmpdir/dir3/file5
git add $tmpdir/dir1/file1
git add $tmpdir/dir1/file2
git add $tmpdir/dir2/file3
git add $tmpdir/dir2/file4
git add $tmpdir/dir3/file5

COMMAND;
        exec($script);

        chdir($tmpdir);        
        $extractor = new ExtractCommitedFiles();
        $extractor->setExcludedPaths(['dir2']);
        $files = $extractor->getFiles();
        $this->assertTrue(in_array('dir1/file1', $files));
        $this->assertTrue(in_array('dir1/file2', $files));
        $this->assertFalse(in_array('dir2/file3', $files));
        $this->assertFalse(in_array('dir2/file4', $files));
        $this->assertTrue(in_array('dir3/file5', $files));
        
        $script = <<<COMMAND
rm -rf $tmpdir
COMMAND;
        exec($script);

    }
}
