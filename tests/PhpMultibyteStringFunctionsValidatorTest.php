<?php
/**
 * Copyright 2016-2018 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\PhpKeywordsValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpKeywordValidationRule;
use PHPUnit\Framework\TestCase;

class PhpMultibyteStringFunctionsValidatorTest extends TestCase
{
    use TempFilesTrait;

    private function _testPhpMbstringKeywords($file)
    {
        $phpKeywordValidationRules = [
            // Not-multibyte keywords
            new PhpKeywordValidationRule(
                T_STRING,
                ['ereg', 'eregi', 'eregi_replace', 'ereg_replace', 'mail', 'split', 'stripos', 'stristr', 'strlen', 'strpos', 'strrchr', 'strripos', 'strrpos', 'strstr', 'strtolower', 'strtoupper', 'substr', 'substr_count'],
                'Not multibyte string function found. Are you sure?',
                Constants::RETURN_CODE_WARNING
            )
        ];

        $outputInterface = new OutputInterfaceMock();
        $validator = new PhpKeywordsValidator([$file], $this->tmpdir, $outputInterface);
        $validator->setValidationRules($phpKeywordValidationRules);
        return $validator->validate();
    }

    public function testPhpMbStringKeywordsOk()
    {
        $filename = __DIR__ . '/testcases/mbstring-functions/PhpMbOk.php';
        $returnValue = $this->_testPhpMbstringKeywords($filename);
        $this->assertEquals(Constants::RETURN_CODE_OK, $returnValue, "PHP file $filename uses mb string functions but validator did not return OK code");
    }

    public function testPhpMbStringKeywordsWarning()
    {
        $iterator = new \DirectoryIterator(__DIR__ . '/testcases/mbstring-functions');
        foreach ($iterator as $file)
        {
            if (!$file->isFile() || $file->getBasename() === 'PhpMbOk.php')
            {
                continue;
            }

            $filePathname = $file->getPathname();
            $returnValue = $this->_testPhpMbstringKeywords($filePathname);
            $this->assertEquals(Constants::RETURN_CODE_WARNING, $returnValue, "PHP file $filePathname does not use mb string functions but validator did not return WARNING code");
        }
    }
}
