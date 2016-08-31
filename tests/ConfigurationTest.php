<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Console\Application;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    private $app;

    protected function setUp()
    {
        parent::setUp();
        $this->app = new Application();
    }

    public function testConfigurationOk()
    {
        $this->app->loadConfiguration(__DIR__ . '/testcases/configuration/php-cqtools.test.yml');
        $configuration = $this->app->getConfiguration();
        $this->assertTrue($configuration->validatePhpSyntax(), 'PHP syntax validation should be enabled');
        $this->assertFalse($configuration->validateForbiddenKeywords(), 'Forbidden keywords validation should be disabled');
        $this->assertTrue($configuration->validateVariableUsage(), 'Variable usage validation should be enabled');
        $this->assertTrue($configuration->validateCodestyle(), 'Codestyle validation should be enabled');
        $this->assertFalse($configuration->validateJsonSyntax(), 'JSON syntax validation should be disabled');
        $this->assertTrue($configuration->validateComposerFilesCommitedTogether(), 'Composer files commit validation should be enabled');
        $this->assertEquals('bitban-extended.xml', $configuration->getCodestyleRuleset(), 'Default codestyle ruleset should be bitban-extended');
    }

    public function testDefaultConfigurationOk()
    {
        $configuration = $this->app->getConfiguration();
        $this->assertTrue($configuration->validatePhpSyntax(), 'PHP syntax validation should be enabled by default');
        $this->assertTrue($configuration->validateForbiddenKeywords(), 'Forbidden keywords validation should be enabled by default');
        $this->assertTrue($configuration->validateVariableUsage(), 'Variable usage validation should be enabled by default');
        $this->assertTrue($configuration->validateCodestyle(), 'Codestyle validation should be enabled by default');
        $this->assertTrue($configuration->validateJsonSyntax(), 'JSON syntax validation should be enabled by default');
        $this->assertTrue($configuration->validateComposerFilesCommitedTogether(), 'Composer files commit validation should be enabled by default');
        $this->assertEquals('rulesets/bitban.xml', $configuration->getCodestyleRuleset(), 'Default codestyle ruleset should be PSR2');
    }

    public function testConfigurationFileSyntaxError()
    {
        $this->setExpectedException('Exception', 'Configuration syntax error');
        $this->app->loadConfiguration(__DIR__ . '/testcases/configuration/php-cqtools.syntaxerror.yml');
        $configuration = $this->app->getConfiguration();
    }
}
