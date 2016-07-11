<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Tests;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OutputInterfaceMock implements OutputInterface
{
    private $output = [];
    
    public function write($messages, $newline = false, $options = 0)
    {
        // TODO: Implement write() method.
    }

    public function writeln($messages, $options = 0)
    {
        // TODO: Implement writeln() method.
    }

    public function setVerbosity($level)
    {
        // TODO: Implement setVerbosity() method.
    }

    public function getVerbosity()
    {
        // TODO: Implement getVerbosity() method.
    }

    public function isQuiet()
    {
        // TODO: Implement isQuiet() method.
    }

    public function isVerbose()
    {
        // TODO: Implement isVerbose() method.
    }

    public function isVeryVerbose()
    {
        // TODO: Implement isVeryVerbose() method.
    }

    public function isDebug()
    {
        // TODO: Implement isDebug() method.
    }

    public function setDecorated($decorated)
    {
        // TODO: Implement setDecorated() method.
    }

    public function isDecorated()
    {
        // TODO: Implement isDecorated() method.
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        // TODO: Implement setFormatter() method.
    }

    public function getFormatter()
    {
        // TODO: Implement getFormatter() method.
    }
}
