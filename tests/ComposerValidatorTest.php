<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\ComposerValidator;

class ComposerValidatorTest extends \PHPUnit_Framework_TestCase
{
    use TempFilesTrait;

    private function getComposerJson($type = 'project')
    {
        return <<<JSON
{
  "name": "bitban/php-code-quality-tools",
  "description": "PHP Code Quality Tools used in Bitban projects.",
  "license": "MIT",
  "keywords": [
    "git",
    "hook",
    "quality"
  ],
  "authors": [
    {
      "name": "Fede Álvarez",
      "email": "falvarez@bitban.com"
    }
  ],
  "type": "{$type}",
  "minimum-stability": "stable",
  "require": {
    "composer/composer": "^1.1"
  },
  "require-dev": {
    "phpunit/phpunit": "^4.8||^5.4"
  },
  "config": {
    "bin-dir": "bin/",
    "optimize-autoloader": true,
    "preferred-install": {
      "*": "dist"
    }
  },
  "bin": [
    "bin/php-cqtools"
  ]
}

JSON;
    }

    private function getComposerLock()
    {
        return <<<JSON
{ "foo": "bar" }

JSON;
    }

    private function getDummy()
    {
        return <<<PHP
<?php

function dummy()
{
    return "foo";
}

PHP;
    }

    /**
     * @param string[] $files
     * @return int
     */
    private function _testComposerFiles($files)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new ComposerValidator($files, $this->tmpdir, $outputInterface);
        return $validator->validate();
    }

    public function testComposerFilesOk()
    {
        $testFiles1 = $this->writeTempFiles([]);
        $returnValue = $this->_testComposerFiles($testFiles1);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'No composer file has been commited but validator did not return OK code');
        $this->cleanTempFiles();

        $testFiles2 = $this->writeTempFiles([
            'composer.json' => $this->getComposerJson(),
            'composer.lock' => $this->getComposerLock(),
            'dummy-file' => $this->getDummy()
        ]);
        $returnValue = $this->_testComposerFiles($testFiles2);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'Both composer files have been commited but validator did not return OK code');
        $this->cleanTempFiles();


        $testFiles3 = $this->writeTempFiles([
            'composer.lock' => $this->getComposerLock(),
            'dummy-file' => $this->getDummy()
        ]);
        $returnValue = $this->_testComposerFiles($testFiles3);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'Only composer.lock has been commited but validator did not return OK code');
        $this->cleanTempFiles();

        $testFiles4 = $this->writeTempFiles([
            'composer.json' => $this->getComposerJson('library'),
            'dummy-file' => $this->getDummy()
        ]);
        $returnValue = $this->_testComposerFiles($testFiles4);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'composer.json was commited, composer.lock was not, project is defined as library but validator did not return OK code');
        $this->cleanTempFiles();
    }

    public function testComposerFilesError()
    {
        $testFiles1 = $this->writeTempFiles([
            'composer.json' => $this->getComposerJson(),
        ]);
        $returnValue = $this->_testComposerFiles($testFiles1);
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'composer.json was commited, composer.lock was not but validator did not return ERROR code');
        $this->cleanTempFiles();
    }
}
