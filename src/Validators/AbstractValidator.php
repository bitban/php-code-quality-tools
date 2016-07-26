<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Interfaces\ValidatorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractValidator
{
    /** @var string */
    protected $basePath;
    /** @var string[] */
    protected $files;
    /** @var OutputInterface */
    protected $output;

    /**
     * @param string[] $files
     * @param string $basePath
     * @param OutputInterface $output
     */
    public function __construct($files, $basePath, OutputInterface $output)
    {
        $this->files = $files;
        $this->basePath = $basePath;
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
     * @return int Result Code = Constants::RETURN_CODE_OK|Constants::RETURN_CODE_WARNING|Constants::RETURN_CODE_ERROR
     */
    public function validate()
    {
        $this->output->writeln(sprintf('<info>%s</info>', $this->getValidatorTitle()));

        $returnCode = Constants::RETURN_CODE_OK;
        
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
