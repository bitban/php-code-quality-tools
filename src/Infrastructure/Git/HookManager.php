<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Infrastructure\Git;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class HookManager
{
    const BACKUP_FILE_EXTENSION = 'bak';

    /** @var Filesystem */
    private $filesystem;
    /** @var OutputInterface */
    private $output;
    /** @var ProgressBar */
    private $progressBar;

    /**
     * HookManager constructor.
     * @param OutputInterface $output @optional
     * @param ProgressBar $progressBar @optional
     */
    public function __construct(OutputInterface $output = null, ProgressBar $progressBar = null)
    {
        $this->filesystem = new Filesystem();

        $this->output = $output;
        $this->progressBar = $progressBar;
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

    public static function getHooksPath()
    {
        return exec('git rev-parse --show-toplevel') . '/.git/hooks';
    }

    private function getHooksSourcePath()
    {
        return realpath(__DIR__ . '/../../../hooks');
    }

    private function backupHook($destinationFile)
    {
        if ($this->filesystem->exists($destinationFile)) {
            $this->filesystem->rename($destinationFile, $destinationFile . '.' . self::BACKUP_FILE_EXTENSION);
            $this->outputWriteln(" <info>Backing up $destinationFile</info>", true);
        }
    }

    private function restoreHook($backupFile)
    {
        $sourceFile = str_replace('.' . self::BACKUP_FILE_EXTENSION, '', $backupFile);
        $this->filesystem->rename($backupFile, $sourceFile);
        $this->outputWriteln(" <info>Restoring $backupFile</info>", true);
    }

    public function installHooks($gitHooksPath)
    {
        $hooksSourcePath = $this->getHooksSourcePath();
        try {

            if (!$this->filesystem->exists($gitHooksPath)) {
                $this->filesystem->mkdir($gitHooksPath);
            }

            $hooks = [];
            foreach (new \DirectoryIterator($hooksSourcePath) as $hook) {
                if ($hook->isDot()) {
                    continue;
                }
                $hooks[] = $hook->getFilename();
            }

            $this->progressBarInit(count($hooks));

            foreach ($hooks as $hook) {
                $sourceFile = $hooksSourcePath . '/' . $hook;
                $destinationFile = $gitHooksPath . '/' . $hook;

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
     * @param string $gitHooksPath
     * @return int
     */
    public function uninstallHooks($gitHooksPath)
    {
        try {
            $hooks = [];
            $backups = [];
            foreach (new \DirectoryIterator($gitHooksPath) as $hook) {
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
                $sourceFile = $gitHooksPath . '/' . $hook;
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
}
