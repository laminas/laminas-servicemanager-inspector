<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin;

use Psalm\CodeLocation;

use function array_key_exists;

final class UnsatisfiedDependencyRegistry
{
    private static $unsatisfiedDependencies = [];

    public static function add(string $serviceId, CodeLocation $codeLocation)
    {
        self::$unsatisfiedDependencies[$serviceId] = $codeLocation;
    }

    public static function getAll(): array
    {
        return self::$unsatisfiedDependencies;
    }

    public static function satisfy(string $serviceId): void
    {
        if (array_key_exists($serviceId, self::$unsatisfiedDependencies)) {
            unset(self::$unsatisfiedDependencies[$serviceId]);
        }
    }
}