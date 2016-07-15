<?php

class PhpCodeStyleValidator extends AbstractValidator
{


    protected function getValidatorTitle()
    {
        return 'Validating PHP code style compliance';
    }

    protected function check($file)
    {
        $ruleset = realpath(__DIR__ . '/../../rulesets/bitban.xml');
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
