<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\PhpMultibyteStringFunctionsValidator;

class PhpMultibyteStringFunctionsValidatorTest extends \PHPUnit_Framework_TestCase
{
    use TempFilesTrait;

    public function testPhpMbStringKeywordsWarning()
    {
        $outputInterface = new OutputInterfaceMock();


        $iterator = new \DirectoryIterator(__DIR__ . '/testcases/mbstring-functions');
        foreach ($iterator as $file)
        {
            if (!$file->isFile())
            {
                continue;
            }

            $filePathname = $file->getPathname();
            $validator = new PhpMultibyteStringFunctionsValidator([$file->getPathname()], $this->tmpdir, $outputInterface);
            $returnValue = $validator->validate();
            $this->assertEquals(Constants::RETURN_CODE_WARNING, $returnValue, "PHP file $filePathname uses not mb string functions but validator did not return WARNING code");
        }
    }
}
