<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;


use Bitban\PhpCodeQualityTools\Interfaces\ValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpSyntaxValidator implements ValidatorInterface
{
    private $files;
    private $output;

    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;
    }

    /**
     * @throws ErrorException
     */
    public function validate()
    {
        $this->output->writeln('<info>Validating PHP syntax</info>');
        
        foreach ($this->files as $file) {
            $process = new Process("php -l $file");
            $process->run();

            if (!$process->isSuccessful()) {
                $this->output->writeln($file);
                $this->output->writeln(sprintf('<error>%s</error>', trim($process->getErrorOutput())));
                
                throw new ErrorException("You have syntax errors in PHP files. Fix them before proceeding!");
            }
        }
    }
}
