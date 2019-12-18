<?php
/**
 * Composer plugin for config assembling
 *
 * @link      https://github.com/hiqdev/composer-config-plugin
 * @package   composer-config-plugin
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2016-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\composer\config\configs;

/**
 * Params class represents output configuration file with params definitions.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Params extends Config
{
    protected function calcValues(array $sources): array
    {
        return $this->pushEnvVars(parent::calcValues($sources));
    }

    protected function pushEnvVars($vars): array
    {
        $env = $this->builder->getConfig('dotenv')->getValues();
        if (!empty($vars)) {
            foreach ($vars as $key => &$value) {
                if (is_array($value)) {
                    foreach (array_keys($value) as $subkey) {
                        $envKey = $this->getEnvKey($key . '_' . $subkey);
                        if (isset($env[$envKey])) {
                            $value[$subkey] = $env[$envKey];
                        }
                    }
                } else {
                    $envKey = $this->getEnvKey($key);
                    if (isset($env[$envKey])) {
                        $vars[$key] = $env[$envKey];
                    }
                }
            }
        }

        return $vars;
    }

    private function getEnvKey(string $key): string
    {
        return strtoupper(strtr($key, '.-', '__'));
    }
}
