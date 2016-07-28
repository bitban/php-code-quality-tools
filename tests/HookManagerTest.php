<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Bitban\PhpCodeQualityTools\Infrastructure\Git\HookManager;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

class HookManagerTest extends PHPUnit_Framework_TestCase
{
    public function testHookUninstall()
    {
        $basePath = vfsStream::setup();
        $hookManager = new HookManager();

        $basePath->addChild(vfsStream::newFile('pre-commit'));
        $basePath->addChild(vfsStream::newFile('post-merge'));
        $basePath->addChild(vfsStream::newFile('post-checkout'));

        $hookManager->uninstallHooks($basePath->url());

        $this->assertFalse($basePath->hasChild('pre-commit'));
        $this->assertFalse($basePath->hasChild('post-merge'));
        $this->assertFalse($basePath->hasChild('post-checkout'));
    }

    public function testHookUninstallRestoreBackups()
    {
        $basePath = vfsStream::setup();
        $hookManager = new HookManager();

        $basePath->addChild(vfsStream::newFile('pre-commit'));
        $basePath->addChild(vfsStream::newFile('post-merge'));
        $basePath->addChild(vfsStream::newFile('post-checkout'));
        $basePath->addChild(vfsStream::newFile('pre-commit.' . HookManager::BACKUP_FILE_EXTENSION));

        $hookManager->uninstallHooks($basePath->url());

        $this->assertTrue($basePath->hasChild('pre-commit'));
        $this->assertFalse($basePath->hasChild('post-merge'));
        $this->assertFalse($basePath->hasChild('post-checkout'));
    }
}
