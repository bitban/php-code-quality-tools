<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\PhpSyntaxValidator;
use PHPUnit_Framework_TestCase;

class PhpSyntaxValidatorTest extends PHPUnit_Framework_TestCase
{
    private function _testPhpSyntax($file)
    {
        $outputInterface = new OutputInterfaceMock();
        $phpSyntaxValidator = new PhpSyntaxValidator([$file], $outputInterface);
        return $phpSyntaxValidator->validate();
    }
    
    public function testPhpSyntaxOK()
    {
        $returnValue = $this->_testPhpSyntax(__DIR__ . '/testcases/syntax/PhpSyntaxOK.php');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP file syntax is right but validator did not return OK code');
    }

    public function testPhpSyntaxError()
    {
        $returnValue = $this->_testPhpSyntax(__DIR__ . '/testcases/syntax/PhpSyntaxError.php');
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'PHP file syntax has errors but validator did not return ERROR code');
    }
}
