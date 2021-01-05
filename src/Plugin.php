<?php

declare(strict_types=1);

namespace Laminas\PsalmPlugin;

use Laminas\PsalmPlugin\Hook\ConfigHook;
use Laminas\PsalmPlugin\Hook\ContainerHook;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

/**
 * @todo WIP
 */
class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__ . '/Hook/ContainerHook.php';
        $registration->registerHooksFromClass(ContainerHook::class);

        require_once __DIR__ . '/Hook/ConfigHook.php';
        $registration->registerHooksFromClass(ConfigHook::class);
    }
}
