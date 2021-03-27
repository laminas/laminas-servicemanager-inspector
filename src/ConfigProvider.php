<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector;

final class ConfigProvider
{
    /**
     * @psalm-return array{
     *     dependencies:array{factories:array<string,mixed>,aliases:array<string,string>},
     *     laminas-cli:array{commands: array{'migration:phpstorm-extended-meta': Command\GenerateCommand::class}}
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getServiceDependencies(),
            'laminas-cli'  => $this->laminasCliConfiguration(),
        ];
    }

    /**
     * @psalm-return array{factories:array<string,mixed>,aliases:array<string,string>}
     */
    public function getServiceDependencies(): array
    {
    }

    /**
     * @psalm-return array{commands: array{'migration:phpstorm-extended-meta': Command\GenerateCommand::class}}
     */
    private function laminasCliConfiguration(): array
    {
    }
}
