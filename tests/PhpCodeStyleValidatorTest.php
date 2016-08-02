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

    private function _testPhpSniffs($file, $customRuleset = null)
    {
        $outputInterface = new OutputInterfaceMock();
        $validator = new PhpCodeStyleValidator([$file], $this->tmpdir, $outputInterface);
        if ($customRuleset !== null) {
            $validator->setRuleset($customRuleset);
        }
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

    public function testPhpCodeStyleValidatorCustomRulesetOk()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/code-style/BitbanCodeStyleCustomOk.php', __DIR__ . '/../rulesets/bitban-extended.xml');
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, 'PHP file code style is right but validator did not return OK code ' . $returnValue);
    }

    public function testPhpCodeStyleValidatorCustomRulesetError()
    {
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/code-style/BitbanCodeStyleError.php', __DIR__ . '/../rulesets/bitban-extended.xml');
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'PHP file code style is wrong but validator did not return ERROR code');
    }

    public function testPhpCodeStyleValidatorMissingCustomRulesetError()
    {
        $ruleset = __DIR__ . '/../rulesets/missing-ruleset.xml';
        $this->setExpectedException('Exception', "Custom ruleset $ruleset not found");
        $returnValue = $this->_testPhpSniffs(__DIR__ . '/testcases/code-style/BitbanCodeStyleError.php', $ruleset);
        $this->assertEquals(Constants::RETURN_CODE_ERROR, $returnValue, 'Custom codestyle file is missing but validator constructor did not throw any exception');
    }
}
