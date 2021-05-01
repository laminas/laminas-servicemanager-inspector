<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Exception;

use LogicException;
use Throwable;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function implode;
use function reset;
use function serialize;
use function sort;
use function sprintf;

final class CyclicAliasException extends LogicException implements ExceptionInterface
{
    public function __construct(array $aliases, ?Throwable $previous = null)
    {
        parent::__construct(self::getDetailedMessage($aliases), 0, $previous);
    }

    private static function getDetailedMessage(array $aliases): string
    {
        $detectedCycles = array_filter(
            array_map(
                function ($alias) use ($aliases) {
                    return self::getCycleFor($aliases, $alias);
                },
                array_keys($aliases)
            )
        );

        if (! $detectedCycles) {
            return sprintf(
                "A cycle was detected within the following aliases map:\n\n%s",
                self::printReferencesMap($aliases)
            );
        }

        return sprintf(
            "Cycles were detected within the provided aliases:\n\n%s\n\n"
            . "The cycle was detected in the following alias map:\n\n%s",
            self::printCycles(self::deDuplicateDetectedCycles($detectedCycles)),
            self::printReferencesMap($aliases)
        );
    }

    /**
     * Retrieves the cycle detected for the given $alias, or `null` if no cycle was detected
     *
     * @param string[] $aliases
     * @param string $alias
     * @return array|null
     */
    private static function getCycleFor(array $aliases, $alias): ?array
    {
        $cycleCandidate = [];
        $targetName     = $alias;

        while (isset($aliases[$targetName])) {
            if (isset($cycleCandidate[$targetName])) {
                return $cycleCandidate;
            }

            $cycleCandidate[$targetName] = true;
            $targetName                  = $aliases[$targetName];
        }

        return null;
    }

    /**
     * @param string[] $aliases
     */
    private static function printReferencesMap(array $aliases): string
    {
        $map = [];

        foreach ($aliases as $alias => $reference) {
            $map[] = '"' . $alias . '" => "' . $reference . '"';
        }

        return "[\n" . implode("\n", $map) . "\n]";
    }

    /**
     * @param string[][] $detectedCycles
     */
    private static function printCycles(array $detectedCycles): string
    {
        return "[\n" . implode("\n", array_map([self::class, 'printCycle'], $detectedCycles)) . "\n]";
    }

    /**
     * @param bool[][] $detectedCycles
     */
    private static function deDuplicateDetectedCycles(array $detectedCycles): array
    {
        $detectedCyclesByHash = [];

        foreach ($detectedCycles as $detectedCycle) {
            $cycleAliases = array_keys($detectedCycle);

            sort($cycleAliases);

            $hash = serialize(array_values($cycleAliases));

            $detectedCyclesByHash[$hash] = $detectedCyclesByHash[$hash] ?? $detectedCycle;
        }

        return array_values($detectedCyclesByHash);
    }

    /**
     * @param string[] $detectedCycle
     */
    private static function printCycle(array $detectedCycle): string
    {
        $fullCycle   = array_keys($detectedCycle);
        $fullCycle[] = reset($fullCycle);

        return implode(
            ' => ',
            array_map(
                function (string $cycle) {
                    return '"' . $cycle . '"';
                },
                $fullCycle
            )
        );
    }
}
