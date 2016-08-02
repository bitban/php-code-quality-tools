<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Infrastructure\Project;
use Symfony\Component\Console\Output\OutputInterface;

class PhpCodeStyleValidator extends AbstractValidator
{
    protected $ruleset;

    /**
     * PhpCodeStyleValidator constructor.
     * @param string[] $files
     * @param string $basePath
     * @param OutputInterface $output
     */
    public function __construct(array $files, $basePath, OutputInterface $output)
    {
        parent::__construct($files, $basePath, $output);
        $this->ruleset = realpath(__DIR__ . '/../../rulesets/bitban.xml');
    }

    /**
     * @param string $ruleset
     * @return PhpCodeStyleValidator
     * @throws \Exception
     */
    public function setRuleset($ruleset)
    {
        if (realpath($ruleset)) {
            $this->ruleset = realpath($ruleset);
        } else {
            throw new \Exception("Custom ruleset $ruleset not found");
        }

        return $this;
    }

    protected function getValidatorTitle()
    {
        return 'Validating PHP code style compliance';
    }

    protected function check($file)
    {
        $binPath = (new Project())->getBinPath();
        $process = $this->buildProcess("$binPath/phpcs --standard=$this->ruleset $file");
        
        $process->run();

        if (!$process->isSuccessful()) {
            $this->output->writeln(sprintf('<error>%s</error>', trim($process->getErrorOutput())));
            $processOutput = $process->getOutput();
            print_r("$binPath/phpcs --standard=$this->ruleset $file");
            print_r($processOutput);
            if (preg_match('/\bERROR\b/', $processOutput)) {
                $exception = new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $processOutput));
            } else {
                $exception = new WarningException(sprintf(Constants::WARNING_MESSAGE_WRAPPER, $processOutput));
            }
            throw $exception;
        }
    }
}
