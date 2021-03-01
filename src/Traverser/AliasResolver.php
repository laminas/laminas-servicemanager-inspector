<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ServiceManager\Inspector\Traverser;

use Laminas\ServiceManager\Inspector\Exception\CyclicAliasException;

final class AliasResolver
{
    /**
     * @param array $aliases
     * @return array
     */
    public function __invoke(array $aliases): array
    {
        $resolvedAliases = [];
        foreach ($aliases as $alias => $service) {
            $visited = [];
            $name = $alias;

            while (isset($aliases[$name])) {
                if (isset($visited[$name])) {
                    throw new CyclicAliasException($aliases);
                }

                $visited[$name] = true;
                $name = $aliases[$name];
            }

            $resolvedAliases[$alias] = $name;
        }

        return $resolvedAliases;
    }
}
