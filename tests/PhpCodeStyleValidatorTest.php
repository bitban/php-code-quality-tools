<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\PhpCodeStyleValidator;

class PhpCodeStyleValidatorTest extends \PHPUnit_Framework_TestCase
{
    use TempFilesTrait;

    private function _testPhpSniffs($file)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new PhpCodeStyleValidator([$file], $this->tmpdir, $outputInterface);
        return $validator->validate();
    }

    public function testPhpCodeStyleValidatorOk()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/code-style/BitbanCodeStyleOk.php');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP file code style is right but validator did not return OK code ' . $returnValue);
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/code-style/BitbanCodeStyleWithAllowedBlankLines.php');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP file has allowed blank lines but validator did not return OK code ' . $returnValue);
    }

    public function testPhpCodeStyleValidatorError()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/code-style/BitbanCodeStyleError.php');
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'PHP file code style is wrong but validator did not return ERROR code');
    }
}
