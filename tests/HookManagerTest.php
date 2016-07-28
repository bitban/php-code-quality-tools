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
    public function testHookInstall()
    {
        $basePath = vfsStream::setup();
        $hookManager = new HookManager();

        $hookManager->installHooks($basePath->url());

        $this->assertTrue($basePath->hasChild('pre-commit'), 'pre-commit hook is not present');
        $this->assertTrue($basePath->hasChild('post-merge'), 'post-merge hook is not present');
        $this->assertTrue($basePath->hasChild('post-checkout'), 'post-checkout hook is not present');
    }

    public function testHookInstallWithBackups()
    {
        $basePath = vfsStream::setup();
        $hookManager = new HookManager();

        $basePath->addChild(vfsStream::newFile('pre-commit'));

        $hookManager->installHooks($basePath->url());

        $this->assertTrue($basePath->hasChild('pre-commit'), 'pre-commit hook is not present');
        $this->assertTrue($basePath->hasChild('post-merge'), 'post-merge hook is not present');
        $this->assertTrue($basePath->hasChild('post-checkout'), 'post-checkout hook is not present');
        $this->assertTrue($basePath->hasChild('pre-commit.' . HookManager::BACKUP_FILE_EXTENSION), 'pre-commit hook has not been backed up');
    }


    public function testHookUninstall()
    {
        $basePath = vfsStream::setup();
        $hookManager = new HookManager();

        $basePath->addChild(vfsStream::newFile('pre-commit'));
        $basePath->addChild(vfsStream::newFile('post-merge'));
        $basePath->addChild(vfsStream::newFile('post-checkout'));

        $hookManager->uninstallHooks($basePath->url());

        $this->assertFalse($basePath->hasChild('pre-commit'), 'pre-commit hook is still present');
        $this->assertFalse($basePath->hasChild('post-merge'), 'post-merge hook is still present');
        $this->assertFalse($basePath->hasChild('post-checkout'), 'post-checkout hook is still present');
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

        $this->assertTrue($basePath->hasChild('pre-commit'), 'pre-commit hook backup has not been restored');
        $this->assertFalse($basePath->hasChild('post-merge'), 'post-merge hook is still present');
        $this->assertFalse($basePath->hasChild('post-checkout'), 'post-checkout hook is still present');
    }
}
