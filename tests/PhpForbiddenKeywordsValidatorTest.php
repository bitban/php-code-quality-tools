<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;


use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\PhpForbiddenKeywordsValidator;

class PhpForbiddenKeywordsValidatorTest extends \PHPUnit_Framework_TestCase
{
    use TempFilesTrait;

    private function _testPhpForbiddenKeywords($file)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new PhpForbiddenKeywordsValidator([$file], $this->tmpdir, $outputInterface);
        return $validator->validate();
    }

    public function testPhpForbiddenKeywordsOk()
    {
        $returnValue = $this->_testPhpForbiddenKeywords(__DIR__ . '/testcases/forbidden-keywords/PhpNoForbiddenKeywords.php');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP file has no forbidden keywords but validator did not return OK code');
    }

    public function testPhpForbiddenKeywordsWarning()
    {
        $returnValue = $this->_testPhpForbiddenKeywords(__DIR__ . '/testcases/forbidden-keywords/PhpEmptyForbiddenKeyword.php');
        $this->assertEquals(Constants::RETURN_CODE_WARNING, $returnValue, 'PHP file has empty() call but validator did not return WARNING code');
    }

    public function testPhpForbiddenKeywordsError()
    {
        $returnValue = $this->_testPhpForbiddenKeywords(__DIR__ . '/testcases/forbidden-keywords/PhpAllForbiddenKeywords.php');
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'PHP file uses all forbidden keywords but validator did not return ERROR code');
        $returnValue = $this->_testPhpForbiddenKeywords(__DIR__ . '/testcases/forbidden-keywords/PhpVarDumpForbiddenKeyword.php');
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'PHP file has var_dump() call but validator did not return ERROR code');
    }
}
