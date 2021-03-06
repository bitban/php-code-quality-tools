<?php
/**
 * Copyright 2016-2018 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\PhpSniffsValidator;
use PHPUnit\Framework\TestCase;

class PhpSniffsValidatorTest extends TestCase
{
    use TempFilesTrait;

    private function _testPhpSniffs($file)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new PhpSniffsValidator([$file], $this->tmpdir, $outputInterface);
        return $validator->validate();
    }

    public function testPhpSniffsOk()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/sniffs/VariableUsageOk.php');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP variable usage is right but validator did not return OK code ' . $returnValue);
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/sniffs/UndefinedVariableUsage.inc');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP variable usage should be skipped for .inc files but validator did not return OK code ' . $returnValue);
    }

    public function testPhpSniffsWarning()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/sniffs/UndefinedVariableUsage.php');
        $this->assertEquals(Constants::RETURN_CODE_WARNING, $returnValue, 'PHP is using an undefined variable but validator did not return WARNING code');
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/sniffs/VariableNotUsed.php');
        $this->assertEquals(Constants::RETURN_CODE_WARNING, $returnValue, 'PHP file has an unused variable but validator did not return WARNING code');
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/sniffs/ThisOutOfClassScopeUsage.php');
        $this->assertEquals(Constants::RETURN_CODE_WARNING, $returnValue, 'PHP file uses $this out of class scope but validator did not return WARNING code');
    }
}
