<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\AliasResolver;

use Laminas\ServiceManager\Inspector\Exception\CyclicAliasException;

use function array_keys;

final class AliasResolver
{
    /**
     * @psalm-param array<string, string> $aliases
     * @param string[] $aliases
     * @psalm-return array<string, string>
     * @return string[]
     */
    public function __invoke(array $aliases): array
    {
        $resolvedAliases = [];
        foreach (array_keys($aliases) as $alias) {
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
