<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\AliasResolver;

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
            $name    = $alias;

            while (isset($aliases[$name])) {
                if (isset($visited[$name])) {
                    // Actually it's never reached - ServiceManager would throw
                    // an exception upon initialization in case of cyclic alias.
                    // It might be useful later when we switch to raw config.php analysis.
                    throw new CyclicAliasException();
                }

                $visited[$name] = true;
                $name           = $aliases[$name];
            }

            $resolvedAliases[$alias] = $name;
        }

        return $resolvedAliases;
    }
}
