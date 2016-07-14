<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Command\Code;

use Bitban\PhpCodeQualityTools\Command\FilesetManipulationCommand;
use Bitban\PhpCodeQualityTools\Constants;
use Bitban\PhpCodeQualityTools\Validators\ComposerValidator;
use Bitban\PhpCodeQualityTools\Validators\JsonValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpCodeStyleValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpForbiddenKeywordsValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpSniffsValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpSyntaxValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends FilesetManipulationCommand
{
    const COMMAND_NAME = 'code:validate';
    const COMMAND_DESCRIPTION = 'Performs all code validations across files in given path';
    const COMMAND_HELP = 'Performs all code validations across files in given path.';
    const ARG_PATH = 'projectPath';
    const OPT_ONLY_COMMITED_FILES = 'only-commited-files';

    protected function configure()
    {
        parent::configure();
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $returnCode = Constants::RETURN_CODE_OK;

        $validators = [];

        if (true === $this->isProcessingAnyComposerFile()) {
            if ($input->getOption(self::OPT_ONLY_COMMITED_FILES)) {
                $validators[] = new ComposerValidator($this->getComposerFiles(), $output);
            }
        }

        if (true === $this->isProcessingAnyPhpFile()) {
            $validators[] = new PhpSyntaxValidator($this->getPhpFiles(), $output);
            $validators[] = new PhpForbiddenKeywordsValidator($this->getPhpFiles(), $output);
            $validators[] = new PhpCodeStyleValidator($this->getPhpFiles(), $output);
            $validators[] = new PhpSniffsValidator($this->getPhpFiles(), $output);
        }

        if (true === $this->isProcessingAnyJsonFile()) {
            $validators[] = new JsonValidator($this->getJsonFiles(), $output);
        }

        foreach ($validators as $validator) {
            $returnCode = max($returnCode, $validator->validate());
        }

        if ($returnCode > 0) {
            $output->writeln('Return code: ' . $returnCode);
        }

        return $returnCode;
    }
}
