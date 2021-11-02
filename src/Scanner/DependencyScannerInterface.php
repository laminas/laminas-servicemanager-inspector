<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Scanner;

use Laminas\ServiceManager\Inspector\Dependency\Dependency;

interface DependencyScannerInterface
{
    /**
     * @psalm-return list<Dependency>
     * @return Dependency[]
     */
    public function scan(string $serviceName): array;

    public function canScan(string $serviceName): bool;
}
