<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\GitHooks\Command;


use Bitban\GitHooks\Infrastructure\Git\ExtractCommitedFiles;
use Bitban\GitHooks\Validators\ComposerValidator;
use Bitban\GitHooks\Validators\ErrorException;
use Bitban\GitHooks\Validators\PhpPsrValidator;
use Bitban\GitHooks\Validators\PhpCodeValidator;
use Bitban\GitHooks\Validators\PhpSyntaxValidator;
use Bitban\GitHooks\Validators\WarningException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PreCommitCommand extends Command
{
    const COMMAND_NAME = 'pre-commit';
    const COMMAND_DESCRIPTION = 'pre-commit Git hook';
    const ARG_PROJECT_PATH = 'projectPath';

    const PHP_FILES_IN_SRC = '/^(.*)(\.php)|(\.inc)$/';
    const JSON_FILES_IN_SRC = '/^(.*)(\.json)$/';
    const COMPOSER_FILES = '/^composer\.(json|lock)$/';

    /** @var  array */
    private $changedFiles = [
        'php' => [],
        'json' => [],
        'composer' => []
    ];
    
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->addArgument(self::ARG_PROJECT_PATH);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running pre-commit hook</info>');

        try {
            $this->extractCommitFiles($output);
            
            if (true === $this->isProcessingAnyComposerFile()) {
                (new ComposerValidator($this->changedFiles['composer'], $output))->validate();
            }

            if (true === $this->isProcessingAnyPhpFile()) {
                (new PhpSyntaxValidator($this->changedFiles['php'], $output))->validate();
                (new PhpCodeValidator($this->changedFiles['php'], $output))->validate();
                (new PhpPsrValidator($this->changedFiles['php'], $output))->validate();
            }

            if (true === $this->isProcessingAnyJsonFile()) {

            }
            
            return 0;
        } catch (WarningException $we) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $output->writeln('<info>' . $we->getMessage() . '</info>');
            }
            return 2;
        } catch (ErrorException $ee) {
            $output->writeln('<error>' . $ee->getMessage() . '</error>');
            return 1;
        }
    }

    private function extractCommitFiles($output)
    {
        $output->write('<info>Fetching changed files...</info>');
        $commitFiles = new ExtractCommitedFiles();
        $changedFiles = $commitFiles->getFiles();

        foreach ($changedFiles as $file) {
            if (preg_match(PreCommitCommand::PHP_FILES_IN_SRC, $file)) {
                $this->changedFiles['php'][] = $file;
            }
            if (preg_match(PreCommitCommand::COMPOSER_FILES, $file)) {
                $this->changedFiles['composer'][] = $file;
            }
            if (preg_match(PreCommitCommand::JSON_FILES_IN_SRC, $file)) {
                $this->changedFiles['json'][] = $file;
            }
        }

        $result = (count($changedFiles) > 1) ? count($changedFiles) . ' files changed' : 'No files changed';
        $output->writeln("<info>$result</info>");

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $output->writeln("<info>Changed files list</info>");
            foreach ($this->changedFiles as $type) {
                foreach ($type as $file) {
                    $output->writeln($file);
                }
            }
        }
    }

    /**
     * @return bool
     */
    private function isProcessingAnyComposerFile()
    {
        return count($this->changedFiles['composer']) > 0;
    }

    /**
     * @return bool
     */
    private function isProcessingAnyPhpFile()
    {
        return count($this->changedFiles['php']) > 0;
    }

    /**
     * @return bool
     */
    private function isProcessingAnyJsonFile()
    {
        return count($this->changedFiles['json']) > 0;
    }
}
