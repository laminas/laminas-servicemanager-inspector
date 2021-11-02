<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

class Module
{
    /** @psalm-return array<string, mixed> */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'service_manager' => $provider->getDependencies(),
            'laminas-cli'     => $provider->getCliConfig(),
        ];
    }
}
