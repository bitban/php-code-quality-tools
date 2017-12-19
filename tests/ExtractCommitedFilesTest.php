<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Infrastructure\Git\ExtractCommitedFiles;

class ExtractCommitedFilesTest extends \PHPUnit_Framework_TestCase
{
    use TempFilesTrait;

    public function testExtractCommitedFiles()
    {
        // wtf "This function does not always add trailing slash. This behaviour is inconsistent across systems, so you have keep an eye on it."
        // @see http://php.net/manual/es/function.sys-get-temp-dir.php
        $tmpdir = $this->tmpdir;

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
touch $tmpdir/.phpstorm.meta.php
git add $tmpdir/dir1/file1
git add $tmpdir/dir1/file2
git add $tmpdir/dir2/file3
git add $tmpdir/dir2/file4
git add $tmpdir/dir3/file5
git add $tmpdir/.phpstorm.meta.php

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
        $this->assertFalse(in_array(".phpstorm.meta.php", $files));
    }
}
