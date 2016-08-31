<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools\Console;

use Bitban\PhpCodeQualityTools\Command\Code\CustomFixCommand;
use Bitban\PhpCodeQualityTools\Command\Code\FixCodeStyleCommand;
use Bitban\PhpCodeQualityTools\Command\Code\ValidateCommand;
use Bitban\PhpCodeQualityTools\Command\Debug\ShowValuesCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\CheckCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\InstallCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostCheckoutCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\PostMergeCommand;
use Bitban\PhpCodeQualityTools\Command\GitHooks\UninstallCommand;
use Bitban\PhpCodeQualityTools\Configuration;
use Bitban\PhpCodeQualityTools\ConfigurationLoader;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Yaml\Exception\ParseException;

class Application extends BaseApplication
{
    const APP_NAME = 'Bitban Technologies PHP Code Quality Tools';
    const APP_VERSION = '0.9.11';

    const PROJECT_CONF_FILENAME = 'php-cqtools.conf.yml';

    protected $configuration = null;
    protected $customConfigurationFile = null;

    public function __construct()
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        $this->addCommands([
            new CheckCommand(),
            new InstallCommand(),
            new PostCheckoutCommand(),
            new PostMergeCommand(),
            new UninstallCommand(),

            new FixCodeStyleCommand(),
            new ValidateCommand(),
            new CustomFixCommand(),

            new ShowValuesCommand()
        ]);

        $this->configuration = ConfigurationLoader::loadFromFile(realpath(__DIR__ . '/../Resources/php-cqtools.conf.default.yml'));
    }

    /**
     * Tries to load configuration from php-cqtools.yml. If not present, uses default configuration
     *
     * @param string $configFile
     * @return Application
     * @throws \Exception if file exists but has syntax errors
     */
    public function loadConfiguration($configFile)
    {
        try {
            $this->configuration = ConfigurationLoader::loadFromFile($configFile);
            $this->customConfigurationFile = $configFile;
        } catch (ParseException $pe) {
            // YAML parse error
            throw new \Exception('Configuration syntax error in file ' . $configFile);
        } catch (\Exception $e) {
            // php-cqtooks.conf.yml file not found
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCustomConfiguration()
    {
        return $this->customConfigurationFile !== null;
    }

    /**
     * @return string|null
     */
    public function getCustomConfigurationFile()
    {
        return $this->customConfigurationFile;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
