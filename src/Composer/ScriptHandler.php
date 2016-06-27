<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\GitHooks\Composer;


use Composer\Script\Event;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function checkHooks(Event $event)
    {
        $options = self::getOptions($event);

        $projectPath = realpath($event->getComposer()->getConfig()->get('vendor-dir') . '/../');
        $libraryPath = realpath(__DIR__ . '/../..');

        $cmd = 'hook:check-hooks';
        $timeout = $options['process-timeout'];

        $php = escapeshellarg(static::getPhp(false));
        $phpArgs = implode(' ', array_map('escapeshellarg', static::getPhpArguments()));
        $console = escapeshellarg('bin/hook-processor');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $command = join(' ', [$console, $cmd, $projectPath, $libraryPath]);
        $process = new Process($php . ($phpArgs ? ' ' . $phpArgs : '') . ' ' . $command, null, null, null, $timeout);
        $process->run(function ($type, $buffer) use ($event) {
            $event->getIO()->write($buffer, false);
        });
        if (!$process->isSuccessful()) {
            $event->stopPropagation();
            throw new \RuntimeException(sprintf("An error occurred when executing the \"%s\" command:\n\n%s\n\n%s.",
                escapeshellarg($cmd), $process->getOutput(), $process->getErrorOutput()));
        }
    }

    /**
     * @param Event $event
     * @return array
     */
    protected static function getOptions(Event $event)
    {
        $options = $event->getComposer()->getPackage()->getExtra();
        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');
        return $options;
    }

    /**
     * @param bool $includeArgs
     * @return string
     */
    protected static function getPhp($includeArgs = true)
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find($includeArgs)) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }
        return $phpPath;
    }

    /**
     * @return array
     */
    protected static function getPhpArguments()
    {
        $arguments = array();
        $phpFinder = new PhpExecutableFinder();
        if (method_exists($phpFinder, 'findArguments')) {
            $arguments = $phpFinder->findArguments();
        }
        if (false !== $ini = php_ini_loaded_file()) {
            $arguments[] = '--php-ini=' . $ini;
        }
        return $arguments;
    }
}
