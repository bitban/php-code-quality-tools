<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Console;

use Bitban\PhpCodeQualityTools\Command\Code\CustomFixCommand;
use Bitban\PhpCodeQualityTools\Command\Code\FixCodeStyleCommand;
use Bitban\PhpCodeQualityTools\Command\Code\ValidateCommand;
use Bitban\PhpCodeQualityTools\Command\Debug\ShowValuesCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\CheckCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\InstallCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostCheckoutCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostMergeCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\UninstallCommand;
use Bitban\PhpCodeQualityTools\Command\Test\RunCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    const APP_NAME = 'Bitban Technologies PHP Code Quality Tools';
    const APP_VERSION = '0.9.13';

    public function __construct()
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        $this->addCommands([
            new CheckCommand(),
            new InstallCommand(),
            new PostCheckoutCommand(),
            new PostMergeCommand(),
            new UninstallCommand(),

            new FixCodeStyleCommand(),
            new ValidateCommand(),
            new CustomFixCommand(),

            new ShowValuesCommand(),

            new RunCommand()
        ]);
    }
}
