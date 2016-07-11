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
    private function _testComposerFiles($files)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new ComposerValidator($files, $outputInterface);
        return $validator->validate();
    }

    public function testComposerFilesOk()
    {
        $files = [];
        $returnValue = $this->_testComposerFiles($files);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'No composer file has been commited but validator did not return OK code');
        $files = [
            'composer.json',
            'composer.lock',
            'dummy-file'
        ];
        $returnValue = $this->_testComposerFiles($files);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'Both composer files have been commited but validator did not return OK code');
        $files = [
            'composer.lock',
            'dummy-file'
        ];
        $returnValue = $this->_testComposerFiles($files);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'Only composer.lock has been commited but validator did not return OK code');
    }

    public function testComposerFilesError()
    {
        $files = [
            'composer.json'
        ];
        $returnValue = $this->_testComposerFiles($files);
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'composer.json was commited, composer.lock was not but validator did not return ERROR code');
    }
}
