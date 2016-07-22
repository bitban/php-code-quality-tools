<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Validators;

use Bitban\PhpCodeQualityTools\Constants;
use Symfony\Component\Console\Output\OutputInterface;

class PhpCodeStyleValidator extends AbstractValidator
{
    protected $customRuleset;

    /**
     * PhpCodeStyleValidator constructor.
     * @param array $files
     * @param OutputInterface $output
     * @param string $customRuleset
     */
    public function __construct(array $files, OutputInterface $output, $customRuleset)
    {
        parent::__construct($files, $output);
        $this->customRuleset = $customRuleset;
    }

    protected function getRulesetPath()
    {
        return realpath(($this->customRuleset !== null) ?$this->customRuleset : __DIR__ . '/../../rulesets/bitban.xml');
    }

    protected function getValidatorTitle()
    {
        return 'Validating PHP code style compliance';
    }

    protected function check($file)
    {
        $ruleset = $this->getRulesetPath();
        $process = $this->buildProcess("php bin/phpcs --standard=$ruleset $file");
        
        $process->run();

        if (!$process->isSuccessful()) {
            $this->output->writeln(sprintf('<error>%s</error>', trim($process->getErrorOutput())));
            $processOutput = $process->getOutput();
            if (preg_match('/\bERROR\b/', $processOutput)) {
                $exception = new ErrorException(sprintf(Constants::ERROR_MESSAGE_WRAPPER, $processOutput));
            } else {
                $exception = new WarningException(sprintf(Constants::WARNING_MESSAGE_WRAPPER, $processOutput));
            }
            throw $exception;
        }
    }
}
