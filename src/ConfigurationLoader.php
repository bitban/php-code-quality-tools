<?php

/**
 * Copyright 2016 Bitban Technologies, S.L.
 * Todos los derechos reservados.
 */

namespace Bitban\PhpCodeQualityTools;

use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{
    /**
     * @param $configFile
     * @return Configuration
     * @throws \Exception
     */
    public static function loadFromFile($configFile)
    {
        if (!is_file($configFile)) {
            throw new \Exception("Configuration file not found $configFile");
        }
        $config = Yaml::parse(@file_get_contents($configFile));

        return new Configuration($config);
    }
}
