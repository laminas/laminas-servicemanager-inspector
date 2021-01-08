<?php

declare(strict_types=1);

namespace Laminas\PsalmPlugin;

use Laminas\PsalmPlugin\Hook\ConfigHook;
use Laminas\PsalmPlugin\Hook\ContainerHook;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

use function class_exists;

/**
 * TODO WIP
 */
final class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        class_exists(ContainerHook::class, true);
        $registration->registerHooksFromClass(ContainerHook::class);
        ContainerHook::init(new PluginConfig((array)$config));

        class_exists(ConfigHook::class, true);
        $registration->registerHooksFromClass(ConfigHook::class);
        ConfigHook::init(new PluginConfig((array)$config));
    }
}
