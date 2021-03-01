<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

use Psalm\Config;
use Zakirullin\Mess\MessInterface;

use function dirname;
use function getcwd;

use const DIRECTORY_SEPARATOR;

final class PluginConfig
{
    /** @var string */
    private $configPath;

    /** @var string */
    private $configKey;

    public function __construct(MessInterface $messedPluginConfig)
    {
        $this->configPath = $messedPluginConfig['configPath']->getString();
        $this->configKey  = $messedPluginConfig['configServiceManagerKey']->getString();
    }

    public function getRelativeDependencyConfigPath(): string
    {
        return $this->configPath;
    }

    public function getDependencyConfigPath(): string
    {
        return $this->getBaseDir() . $this->configPath;
    }

    public function getDependencyConfig(): DependencyConfig
    {
        $config = require $this->getDependencyConfigPath();

        return new DependencyConfig($config[$this->configKey] ?? []);
    }

    private function getBaseDir(): string
    {
        $currentDir = (string) getcwd() . DIRECTORY_SEPARATOR;
        $configPath = Config::locateConfigFile($currentDir);

        return dirname($configPath) . DIRECTORY_SEPARATOR;
    }
}
