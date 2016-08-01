<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command;

use Bitban\PhpCodeQualityTools\Infrastructure\Project;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
    const ARG_PATH = 'projectPath';

    protected $projectBasepath;

    protected function configure()
    {
        $this
            ->addArgument(self::ARG_PATH, InputArgument::OPTIONAL, 'Path to be processed', (new Project())->getBasepath());
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->projectBasepath = realpath($input->getArgument(self::ARG_PATH));
        if (!$this->projectBasepath) {
            throw new \Exception('Invalid project basepath ' . $input->getArgument(self::ARG_PATH));
        }
    }
}
