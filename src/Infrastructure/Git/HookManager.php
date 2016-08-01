<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Infrastructure\Git;

use Bitban\PhpCodeQualityTools\Command\GitHooks\InstallCommand;
use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class HookManager
{
    const BACKUP_FILE_EXTENSION = 'bak';

    const BAD_PROJECT_PATH_EXCEPTION_CODE = 1;
    const BAD_HOOKS_SOURCE_PATH_EXCEPTION_CODE = 2;
    const BAD_HOOKS_DESTINATION_PATH_EXCEPTION_CODE = 3;

    /** @var string */
    private $projectBasePath;
    /** @var string */
    private $hooksSourcePath;
    /** @var string */
    private $hooksDestinationPath;

    /** @var Filesystem */
    private $filesystem;

    /** @var OutputInterface */
    private $output = null;
    /** @var ProgressBar */
    private $progressBar = null;

    /**
     * HookManager constructor.
     * @param string $projectBasePath
     * @param string $hooksSourcePath
     * @param string $hooksDestinationPath
     * @throws \Exception
     */
    public function __construct($projectBasePath, $hooksSourcePath, $hooksDestinationPath)
    {
        $this->filesystem = new Filesystem();

        $this->projectBasePath = $projectBasePath;
        $this->hooksSourcePath = $hooksSourcePath;
        $this->hooksDestinationPath = $hooksDestinationPath;

        // Parameter check
        if (!$this->filesystem->exists($projectBasePath)) {
            throw new \Exception("Could not find project base path in $projectBasePath", self::BAD_PROJECT_PATH_EXCEPTION_CODE);
        } elseif (!$this->filesystem->exists($hooksSourcePath)) {
            throw new \Exception("Could not find hooks source in $hooksSourcePath", self::BAD_HOOKS_SOURCE_PATH_EXCEPTION_CODE);
        }
        // .git/hooks should exist but may be not
    }

    /**
     * @return HookManager
     */
    public static function getDefaultInstance()
    {
        return new self(GitHelper::getProjectBasepath(), GitHelper::getHooksSourcePath(), GitHelper::getHooksPath());
    }

    /**
     * @param OutputInterface $output
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @param ProgressBar $progressBar
     * @return $this
     */
    public function setProgressBar(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
        return $this;
    }

    private function progressBarInit($count)
    {
        if ($this->progressBar === null) {
            return;
        }

        $this->progressBar->start($count);
        $this->progressBar->setBarCharacter(Constants::CHARACTER_PROGRESS_BAR);
        $this->progressBar->setProgressCharacter(Constants::CHARACTER_BEER);
    }

    private function progressBarAdvance()
    {
        if ($this->progressBar === null) {
            return;
        }

        $this->progressBar->advance();
    }

    private function progressBarFinish()
    {
        if ($this->progressBar === null) {
            return;
        }

        $this->progressBar->finish();
    }

    private function outputWriteln($message, $checkVerbosity = false)
    {
        if ($this->output === null) {
            return;
        }

        if (!$checkVerbosity || $this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $this->output->writeln($message);
        }
    }

    private function decorateOutput()
    {
        if ($this->output === null) {
            return;
        }

        $this->output->setDecorated(true);
    }

    private function backupHook($destinationFile)
    {
        if ($this->filesystem->exists($destinationFile)) {
            $destinationFileBackup = $destinationFile . '.' . self::BACKUP_FILE_EXTENSION;
            if ($this->filesystem->exists($destinationFileBackup)) {
                $this->outputWriteln(" <info>$destinationFileBackup will be overwritten</info>", true);
            }
            $this->filesystem->rename($destinationFile, $destinationFileBackup, true);
            $this->outputWriteln(" <info>Backing up $destinationFile</info>", true);
        }
    }

    private function restoreHook($backupFile)
    {
        $sourceFile = str_replace('.' . self::BACKUP_FILE_EXTENSION, '', $backupFile);
        $this->filesystem->rename($backupFile, $sourceFile);
        $this->outputWriteln(" <info>Restoring $backupFile</info>", true);
    }

    /**
     * @return int 0 if OK, 1 if ERROR
     */
    public function installHooks()
    {
        try {

            if (!$this->filesystem->exists($this->hooksDestinationPath)) {
                $this->filesystem->mkdir($this->hooksDestinationPath);
            }

            $hooks = [];
            foreach (new \DirectoryIterator($this->hooksSourcePath) as $hook) {
                if ($hook->isDot()) {
                    continue;
                }
                $hooks[] = $hook->getFilename();
            }

            $this->progressBarInit(count($hooks));

            foreach ($hooks as $hook) {
                $sourceFile = $this->hooksSourcePath . '/' . $hook;
                $destinationFile = $this->hooksDestinationPath . '/' . $hook;

                $this->backupHook($destinationFile);

                $this->filesystem->copy($sourceFile, $destinationFile, true);
                $this->filesystem->chmod($destinationFile, 0755);

                $this->progressBarAdvance();

                $this->outputWriteln(" <info>Copying $sourceFile to $destinationFile</info>", true);
            }

            $this->progressBarFinish();

            $this->outputWriteln('');
            $this->outputWriteln(sprintf('<info>Hooks installed succesfully %s</info>', Constants::CHARACTER_THUMB_UP));
        } catch (\Exception $e) {
            $this->outputWriteln(" <error>" . $e->getMessage() . " Aborting</error>");
            return 1;
        }

        return 0;
    }

    /**
     * @return int 0 if OK, 1 if ERROR
     */
    public function uninstallHooks()
    {
        try {
            $hooks = [];
            $backups = [];
            foreach (new \DirectoryIterator($this->hooksDestinationPath) as $hook) {
                if ($hook->isDot()) {
                    continue;
                }
                if ($hook->getExtension() === self::BACKUP_FILE_EXTENSION) {
                    $backups[] = $hook->getFileInfo();
                } else {
                    $hooks[] = $hook->getFilename();
                }
            }

            $this->progressBarInit(count($hooks) + count($backups));

            foreach ($hooks as $hook) {
                $sourceFile = $this->hooksDestinationPath . '/' . $hook;
                $this->filesystem->remove($sourceFile);

                $this->progressBarAdvance();
                $this->outputWriteln(" <info>Removing $sourceFile</info>", true);
            }

            foreach ($backups as $backup) {
                $this->restoreHook($backup);
                $this->progressBarAdvance();
            }

            $this->progressBarFinish();
            $this->outputWriteln('');
            $this->outputWriteln(sprintf('<info>Hooks uninstalled succesfully %s</info>', Constants::CHARACTER_THUMB_UP));
        } catch (\Exception $e) {
            $this->outputWriteln(" <error>" . $e->getMessage() . " Aborting</error>");
            return 1;
        }

        return 0;
    }

    /**
     * @param bool $skipOkMessage
     * @return int 0 if OK, 1 if ERROR
     * @throws \Exception
     */
    public function checkHooks($skipOkMessage = true)
    {
        if (!$this->filesystem->exists($this->hooksDestinationPath)) {
            throw new \Exception("Cannot find hooks installation path: $this->hooksDestinationPath", self::BAD_HOOKS_DESTINATION_PATH_EXCEPTION_CODE);
        }

        $result = true;
        try {
            foreach (new \DirectoryIterator($this->hooksSourcePath) as $hook) {
                if ($hook->isDot()) {
                    continue;
                }

                $sourceFile = $this->hooksSourcePath . '/' . $hook;
                $destinationFile = $this->hooksDestinationPath . '/' . $hook;

                $filesMatch = (@file_get_contents($sourceFile) === @file_get_contents($destinationFile));

                $result = $result && $filesMatch;

                $this->outputWriteln("<info>Comparing $sourceFile with $destinationFile</info> " .
                    ($filesMatch ? Constants::CHARACTER_OK : Constants::CHARACTER_KO), true);
            }
        } catch (\Exception $e) {
            $this->outputWriteln("<error>" . $e->getMessage() . "</error>");
            $result = false;
        }

        $this->decorateOutput();

        if (!$result) {
            $installCommand = InstallCommand::COMMAND_NAME;
            $this->outputWriteln("<error>Your hooks are not properly configured!</error>\n");
            $this->outputWriteln("<comment>You may install them running the folowing command:\n\n$this->projectBasePath/bin/php-cqtools $installCommand\n</comment>");
            $result = false;
        } else {
            if (!$skipOkMessage) {
                $this->outputWriteln("<info>Your hooks are properly set. Nice job!</info> " . Constants::CHARACTER_THUMB_UP);
            }
        }

        return $result ? 0 : 1;
    }
}
