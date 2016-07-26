<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;


use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\JsonValidator;

class JsonValidatorTest extends \PHPUnit_Framework_TestCase
{
    use TempFilesTrait;

    private function _testJsonSyntax($file)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new JsonValidator([$file], $this->tmpdir, $outputInterface);
        return $validator->validate();
    }

    public function testJsonSyntaxOk()
    {
        $returnValue = $this->_testJsonSyntax(__DIR__ . '/testcases/syntax/JsonOk.json');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'JSON file syntax is right but validator did not return OK code');
    }

    public function testJsonSyntaxError()
    {
        $testFiles = [
            __DIR__ . '/testcases/syntax/JsonError_missingComma.json',
            __DIR__ . '/testcases/syntax/JsonError_missingKeyQuotes.json',
            __DIR__ . '/testcases/syntax/JsonError_trailingComma.json'
        ];
        foreach ($testFiles as $testFile) {
            $returnValue = $this->_testJsonSyntax($testFile);
            $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'JSON file syntax has errors but validator did not return ERROR code');
        }
    }
}
