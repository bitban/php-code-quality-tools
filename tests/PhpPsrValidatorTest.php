<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;


use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\PhpPsrValidator;

class PhpPsrValidatorTest extends \PHPUnit_Framework_TestCase
{
    private function _testPhpSniffs($file)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new PhpPsrValidator([$file], $outputInterface);
        return $validator->validate();
    }

    public function testPhpPsrValidatorOk()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/psr2/Psr2CodeStyleOk.php');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP file code style is right but validator did not return OK code ' . $returnValue);
    }

    public function testPhpPsrValidatorError()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/psr2/Psr2CodeStyleError.php');
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'PHP file code style is wrong but validator did not return ERROR code');
    }
}
