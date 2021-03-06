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
use Bitban\PhpCodeQualityTools\Validators\PhpKeywordsValidator;
use Bitban\PhpCodeQualityTools\Validators\PhpKeywordValidationRule;
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

        if (true === $this->isProcessingAnyComposerFile()) {
            if ($input->getOption(self::OPT_ONLY_COMMITED_FILES)) {
                $validators[] = new ComposerValidator($this->getComposerFiles(), $projectPath, $output);
            }
        }

        if (true === $this->isProcessingAnyPhpFile()) {

            $phpKeywordValidationRules = [
                // Forbidden kewyords
                new PhpKeywordValidationRule(T_STRING, ['var_dump'], 'var_dump() function call found', Constants::RETURN_CODE_ERROR),
                new PhpKeywordValidationRule(T_EMPTY, ['empty'], 'empty() operator found', Constants::RETURN_CODE_WARNING),
                // Not-multibyte keywords
                new PhpKeywordValidationRule(
                    T_STRING,
                    ['ereg', 'eregi', 'eregi_replace', 'ereg_replace', 'mail', 'split', 'stripos', 'stristr', 'strlen', 'strpos', 'strrchr', 'strripos', 'strrpos', 'strstr', 'strtolower', 'strtoupper', 'substr', 'substr_count'],
                    'Not multibyte string function found. Are you sure?',
                    Constants::RETURN_CODE_WARNING
                )
            ];

            $validators[] = new PhpSyntaxValidator($this->getPhpFiles(), $projectPath, $output);
            $validators[] = (new PhpKeywordsValidator($this->getPhpFiles(), $projectPath, $output))
                ->setValidationRules($phpKeywordValidationRules);

            $phpCodeStyleValidator = new PhpCodeStyleValidator($this->getPhpFiles(), $projectPath, $output);
            if ($input->getOption(self::OPT_CUSTOM_RULESET) !== null) {
                $phpCodeStyleValidator->setRuleset($input->getOption(self::OPT_CUSTOM_RULESET));
            }
            $validators[] = $phpCodeStyleValidator;

            $validators[] = new PhpSniffsValidator($this->getPhpFiles(), $projectPath, $output);
        }

        if (true === $this->isProcessingAnyJsonFile()) {
            $validators[] = new JsonValidator($this->getJsonFiles(), $projectPath, $output);
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
