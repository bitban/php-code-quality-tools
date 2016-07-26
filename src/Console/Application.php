<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Console;

use Bitban\PhpCodeQualityTools\Command\Code\FixCodeStyleCommand;
use Bitban\PhpCodeQualityTools\Command\Code\ValidateCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\CheckCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\InstallCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostCheckoutCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostMergeCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    const APP_NAME = 'Bitban Technologies PHP Code Quality Tools';

    private $localVersion;
    private $remoteVersion;

    public function __construct()
    {
        $this->remoteVersion = exec('git describe `git remote` --abbrev=0 --tags');
        $this->localVersion = exec('git describe --abbrev=0 --tags');

        parent::__construct(self::APP_NAME, trim($this->localVersion, 'v'));

        $this->addCommands([
            new CheckCommand(),
            new InstallCommand(),
            new PostCheckoutCommand(),
            new PostMergeCommand(),

            new FixCodeStyleCommand(),
            new ValidateCommand()
        ]);
    }

    private function checkVersion(OutputInterface $output)
    {
        $versionDiff = version_compare($this->remoteVersion, $this->localVersion);
        if ($versionDiff > 0) {
            $output->writeln("\n<error>You don't have the latest version of Php Code Quality Tools. Please update to $this->remoteVersion.</error>\n");
        }
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->checkVersion($output);
        return parent::doRun($input, $output);
    }
}
