<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

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
