<?php
/**
 * Copyright 2016-2018 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools;

use Bitban\PhpCodeQualityTools\Infrastructure\Git\GitHelper;
use Bitban\PhpCodeQualityTools\Infrastructure\Git\HookManager;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class HookManagerTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $basePath;

    protected function setUp()
    {
        $this->basePath = vfsStream::setup();
        $this->basePath->addChild(vfsStream::newDirectory('project'));
        $this->basePath->addChild(vfsStream::newDirectory('hooks'));
        $this->basePath->addChild(vfsStream::newDirectory('.git/hooks'));
    }

    protected function getHookManager()
    {
        return new HookManager(
            $this->basePath->getChild('project')->url(),
            GitHelper::getHooksSourcePath(),
            $this->basePath->getChild('.git/hooks')->url()
        );
    }

    public function testHookManagerConstructor()
    {
        $hookManager = new HookManager(
            $this->basePath->getChild('project')->url(),
            $this->basePath->getChild('hooks')->url(),
            'bar'
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(HookManager::BAD_PROJECT_PATH_EXCEPTION_CODE);
        $hookManager = new HookManager('foo', 'bar', 'baz');

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(HookManager::BAD_HOOKS_SOURCE_PATH_EXCEPTION_CODE);
        $hookManager = new HookManager($this->basePath->getChild('project')->url(), 'foo', 'bar');
    }

    public function testHookInstall()
    {
        $hookManager = $this->getHookManager();

        $hookManager->installHooks();

        /** @var vfsStreamDirectory $hooksDestinationPath */
        $hooksDestinationPath = $this->basePath->getChild('.git/hooks');
        $this->assertTrue($hooksDestinationPath->hasChild('pre-commit'), 'pre-commit hook is not present');
        $this->assertTrue($hooksDestinationPath->hasChild('post-merge'), 'post-merge hook is not present');
        $this->assertTrue($hooksDestinationPath->hasChild('post-checkout'), 'post-checkout hook is not present');
        $this->assertEquals(3, count($hooksDestinationPath->getChildren()), 'hook files count is not right');
    }

    public function testHookInstallWithBackups()
    {
        $hookManager = $this->getHookManager();

        $hooksDestinationPath = $this->basePath->getChild('.git/hooks');

        /** @var vfsStreamDirectory $hooksDestinationPath */
        $hooksDestinationPath->addChild(vfsStream::newFile('pre-commit'));

        $hookManager->installHooks();

        $this->assertTrue($hooksDestinationPath->hasChild('pre-commit'), 'pre-commit hook is not present');
        $this->assertTrue($hooksDestinationPath->hasChild('post-merge'), 'post-merge hook is not present');
        $this->assertTrue($hooksDestinationPath->hasChild('post-checkout'), 'post-checkout hook is not present');
        $this->assertTrue($hooksDestinationPath->hasChild('pre-commit.' . HookManager::BACKUP_FILE_EXTENSION), 'pre-commit hook has not been backed up');
    }


    public function testHookUninstall()
    {
        $hookManager = $this->getHookManager();

        /** @var vfsStreamDirectory $hooksDestinationPath */
        $hooksDestinationPath = $this->basePath->getChild('.git/hooks');

        $hooksDestinationPath->addChild(vfsStream::newFile('pre-commit'));
        $hooksDestinationPath->addChild(vfsStream::newFile('post-merge'));
        $hooksDestinationPath->addChild(vfsStream::newFile('post-checkout'));

        $hookManager->uninstallHooks();

        $this->assertFalse($hooksDestinationPath->hasChild('pre-commit'), 'pre-commit hook is still present');
        $this->assertFalse($hooksDestinationPath->hasChild('post-merge'), 'post-merge hook is still present');
        $this->assertFalse($hooksDestinationPath->hasChild('post-checkout'), 'post-checkout hook is still present');
    }

    public function testHookUninstallRestoreBackups()
    {
        $hookManager = $this->getHookManager();

        /** @var vfsStreamDirectory $hooksDestinationPath */
        $hooksDestinationPath = $this->basePath->getChild('.git/hooks');

        $hooksDestinationPath->addChild(vfsStream::newFile('pre-commit'));
        $hooksDestinationPath->addChild(vfsStream::newFile('post-merge'));
        $hooksDestinationPath->addChild(vfsStream::newFile('post-checkout'));
        $hooksDestinationPath->addChild(vfsStream::newFile('pre-commit.' . HookManager::BACKUP_FILE_EXTENSION));

        $hookManager->uninstallHooks();

        $this->assertTrue($hooksDestinationPath->hasChild('pre-commit'), 'pre-commit hook backup has not been restored');
        $this->assertFalse($hooksDestinationPath->hasChild('post-merge'), 'post-merge hook is still present');
        $this->assertFalse($hooksDestinationPath->hasChild('post-checkout'), 'post-checkout hook is still present');
    }

    public function testHookCheck()
    {
        $hookManager = $this->getHookManager();

        $this->assertEquals(1, $hookManager->checkHooks(), 'Hooks should not be installed yet');

        $hookManager->installHooks();

        $this->assertEquals(0, $hookManager->checkHooks(), 'Hooks should be installed but they are not');
    }

    public function testHookCheckExceptions()
    {
        $hookManager = new HookManager(
            $this->basePath->getChild('project')->url(),
            GitHelper::getHooksSourcePath(),
            'foo'
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(HookManager::BAD_HOOKS_DESTINATION_PATH_EXCEPTION_CODE);

        $hookManager->checkHooks();
    }
}
