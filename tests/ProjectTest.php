<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Infrastructure\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    use TempFilesTrait;

    /** @var Project */
    private $project;

    private function setUpTempFiles()
    {
        $this->createTempDirectories(['dir1', 'dir2', 'dir3/dir4']);
        $this->writeTempFiles([
                'file1.php' => '',
                'file2.php' => '',
                'file3.php' => '',
                'file4.txt' => ''
            ], 'dir1');
        $this->writeTempFiles([
                'file5.php' => '',
                'file6.php' => '',
                'file7.txt' => ''
            ], 'dir2');
        $this->writeTempFiles([
                'file8.php' => '',
                'file9.php' => ''
            ], 'dir3/dir4');

        $this->project = new Project($this->tmpdir);
    }

    public function testInvalidFile()
    {
        $this->setUpTempFiles();
        $this->setExpectedException('InvalidArgumentException');
        $files = $this->project->listFiles($this->tmpdir . '/invalidFile.php');
    }

    public function testInvalidPath()
    {
        $this->setUpTempFiles();
        $this->setExpectedException('InvalidArgumentException');
        $files = $this->project->listFiles($this->tmpdir . '/foo');
    }

    public function testSingleFile()
    {
        $this->setUpTempFiles();
        $files = $this->project->listFiles($this->tmpdir . '/dir1/file3.php');
        $numFiles = count($files);
        $this->assertEquals(1, $numFiles, "1 file should be returned, but $numFiles were");
        $this->assertEquals(realpath($this->tmpdir . '/dir1/file3.php'), $files[0], 'dir1/file3.php should be returned, but it is not');
    }

    public function testListFiles()
    {
        $this->setUpTempFiles();
        $files = $this->project->listFiles($this->tmpdir . '/dir1');
        $numFiles = count($files);
        $this->assertEquals(3, $numFiles, "3 files should be returned, but $numFiles were");
        $this->assertContains(realpath($this->tmpdir . '/dir1/file1.php'), $files, 'dir1/file1.php should be returned but it is not');
        $this->assertContains(realpath($this->tmpdir . '/dir1/file2.php'), $files, 'dir1/file2.php should be returned but it is not');
        $this->assertContains(realpath($this->tmpdir . '/dir1/file3.php'), $files, 'dir1/file3.php should be returned but it is not');
        $this->assertNotContains(realpath($this->tmpdir . '/dir1/file4.txt'), $files, 'dir1/file4.txt should not be returned but it is');
    }

    public function testListFilesWithExclusions()
    {
        $this->setUpTempFiles();
        $files = $this->project->listFiles($this->tmpdir, ['dir1', 'dir3/dir4']);
        $numFiles = count($files);
        $this->assertEquals(2, $numFiles, "2 files should be returned, but $numFiles were");
        $this->assertContains(realpath($this->tmpdir . '/dir2/file5.php'), $files, 'dir2/file5.php should be returned but it is not');
        $this->assertContains(realpath($this->tmpdir . '/dir2/file6.php'), $files, 'dir2/file6.php should be returned but it is not');
        $this->assertNotContains(realpath($this->tmpdir . '/dir1/file1.php'), $files, 'dir1/file1.php should not be returned but it is');
        $this->assertNotContains(realpath($this->tmpdir . '/dir2/file7.txt'), $files, 'dir2/file7.txt should not be returned but it is');
    }
}
