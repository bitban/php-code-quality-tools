<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Console;

use Bitban\PhpCodeQualityTools\Command\CodeStyle\FixCodeStyleCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\CheckCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\InstallCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostCheckoutCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostMergeCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PreCommitCommand;
use Bitban\PhpCodeQualityTools\PhpCodeQualityTools;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct(PhpCodeQualityTools::APP_NAME, PhpCodeQualityTools::APP_VERSION);

        // project:create
        $this->addCommands([
            new CheckCommand(),
            new InstallCommand(),
            new PostCheckoutCommand(),
            new PostMergeCommand(),
            new PreCommitCommand(),

            new FixCodeStyleCommand()
        ]);
    }
}
