<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

use Laminas\ServiceManager\Inspector\Hook\ConfigHook;
use Laminas\ServiceManager\Inspector\Hook\ContainerHook;
use Psalm\Exception\ConfigException;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;
use Throwable;
use Zakirullin\Mess\Mess;

use function class_exists;

final class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        try {
            $this->init($registration, $config);
        } catch (Throwable $e) {
            throw new ConfigException($e->getMessage(), 0, $e);
        }
    }

    private function init(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $messedPluginConfig = new Mess((array) $config);
        $pluginConfig       = new PluginConfig($messedPluginConfig);

        class_exists(ContainerHook::class, true);
        $registration->registerHooksFromClass(ContainerHook::class);
        ContainerHook::init($pluginConfig);

        class_exists(ConfigHook::class, true);
        $registration->registerHooksFromClass(ConfigHook::class);
        ConfigHook::init($pluginConfig);
    }
}
