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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends FilesetManipulationCommand
{
    const COMMAND_NAME = 'code:validate';
    const COMMAND_DESCRIPTION = 'Performs all code validations across files in given path';
    const COMMAND_HELP = 'Performs all code validations across files in given path.';
    const ARG_PATH = 'projectPath';
    const OPT_ONLY_COMMITED_FILES = 'only-commited-files';
    const OPT_CUSTOM_RULESET = 'custom-ruleset';

    protected function configure()
    {
        parent::configure();
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setHelp(self::COMMAND_HELP)
            ->addOption(self::OPT_CUSTOM_RULESET, null, InputOption::VALUE_OPTIONAL, 'If present, uses PHP Code Sniffer with custom ruleset');
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

        $projectPath = $input->getArgument(self::ARG_PATH);

        // Composer files

        if (true === $this->isProcessingAnyComposerFile()) {

            if ($input->getOption(self::OPT_ONLY_COMMITED_FILES) && $this->configuration->validateComposerFilesCommitedTogether()) {
                $validators[] = new ComposerValidator($this->getComposerFiles(), $projectPath, $output);
            } else {
                $output->writeln('<info>' . ComposerValidator::getDisabledText() . '</info>');
            }

        }

        // PHP files

        if (true === $this->isProcessingAnyPhpFile()) {

            // Syntax validation
            if ($this->configuration->validatePhpSyntax()) {
                $validators[] = new PhpSyntaxValidator($this->getPhpFiles(), $projectPath, $output);
            } else {
                $output->writeln('<info>' . PhpSyntaxValidator::getDisabledText() . '</info>');
            }

            // Forbidden keywords validation
            if ($this->configuration->validateForbiddenKeywords()) {
                $validators[] = new PhpForbiddenKeywordsValidator($this->getPhpFiles(), $projectPath, $output);
            } else {
                $output->writeln('<info>' . PhpForbiddenKeywordsValidator::getDisabledText() . '</info>');
            }

            if ($this->configuration->validateCodestyle()) {
                $phpCodeStyleValidator = new PhpCodeStyleValidator($this->getPhpFiles(), $projectPath, $output);
                $phpCodeStyleValidator->setRuleset($this->configuration->getCodestyleRuleset());
                if ($input->getOption(self::OPT_CUSTOM_RULESET) !== null) {
                    $phpCodeStyleValidator->setRuleset($input->getOption(self::OPT_CUSTOM_RULESET));
                }
                $validators[] = $phpCodeStyleValidator;
            } else {
                $output->writeln('<info>' . PhpCodeStyleValidator::getDisabledText() . '</info>');
            }

            if ($this->configuration->validateVariableUsage()) {
                $validators[] = new PhpSniffsValidator($this->getPhpFiles(), $projectPath, $output);
            } else {
                $output->writeln('<info>' . PhpSniffsValidator::getDisabledText() . '</info>');
            }

        }

        // JSON files

        if (true === $this->isProcessingAnyJsonFile()) {

            if ($this->configuration->validateJsonSyntax()) {
                $validators[] = new JsonValidator($this->getJsonFiles(), $projectPath, $output);
            } else {
                $output->writeln('<info>' . JsonValidator::getDisabledText() . '</info>');
            }

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
