<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractValidator
{
    /** @var string[] */
    protected $files;
    /** @var OutputInterface */
    protected $output;

    /**
     * @param string[] $files
     * @param OutputInterface $output
     */
    public function __construct($files, OutputInterface $output)
    {
        $this->files = $files;
        $this->output = $output;
    }

    /**
     * @param $command
     * @return Process
     */
    protected function buildProcess($command)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $this->output->writeln("<info>Running: $command</info>");
        }
        return new Process($command);
    }

    /**
     * @return int
     * @throws WarningException
     * @throws ErrorException
     */
    public function validate()
    {
        $this->output->writeln(sprintf('<info>%s</info>', $this->getValidatorTitle()));

        $returnCode = 0;
        
        foreach ($this->files as $file) {
            if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $this->output->writeln('Processing file ' . $file);
            }
            try {
                $this->check($file);
                if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                    $this->output->writeln('File OK ' . Constants::CHARACTER_THUMB_UP);
                }
            } catch (WarningException $we) {
                $this->output->writeln($we->getMessage());
                $returnCode = max($returnCode, Constants::RETURN_CODE_WARNING);
            } catch (ErrorException $ee) {
                $this->output->writeln($ee->getMessage());
                $returnCode = max($returnCode, Constants::RETURN_CODE_ERROR);
            }
        }
        
        return $returnCode;
    }

    /**
     * @return string
     */
    abstract protected function getValidatorTitle();

    /**
     * @param string $file
     * @throws WarningException
     * @throws ErrorException
     */
    abstract protected function check($file);
}
