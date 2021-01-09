<?php

declare(strict_types=1);

/**
 * @see       https://github.com/laminas/laminas-servicemanager for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\PsalmPlugin\Exception;

use LogicException;

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
    /**
     * @psalm-var array<string, string>
     *
     * @param string[] $aliases
     *
     * @return self
     */
    public static function fromAliasesMap(array $aliases): self
    {
        $detectedCycles = array_filter(
            array_map(
                function ($alias) use ($aliases) {
                    return self::getCycleFor($aliases, $alias);
                },
                array_keys($aliases)
            )
        );

        $message = sprintf(
            "Cycles were detected within the provided aliases:\n\n%s\n\n"
            . "The cycle was detected in the following alias map:\n\n%s",
            self::printCycles(self::deDuplicateDetectedCycles($detectedCycles)),
            self::printReferencesMap($aliases)
        );
        if (!$detectedCycles) {
            $message = sprintf(
                    "A cycle was detected within the following aliases map:\n\n%s",
                    self::printReferencesMap($aliases)
                );
        }

        return new self($message);
    }

    /**
     * @param string[] $aliases
     * @param string $alias
     *
     * @return array|null
     */
    private static function getCycleFor(array $aliases, $alias): ?array
    {
        $cycleCandidate = [];
        $targetName = $alias;

        while (isset($aliases[$targetName])) {
            if (isset($cycleCandidate[$targetName])) {
                return $cycleCandidate;
            }

            $cycleCandidate[$targetName] = true;

            $targetName = $aliases[$targetName];
        }

        return null;
    }

    /**
     * @param string[][] $detectedCycles
     *
     * @return string
     */
    private static function printCycles(array $detectedCycles): string
    {
        return "[\n" . implode("\n", array_map([__CLASS__, 'printCycle'], $detectedCycles)) . "\n]";
    }

    /**
     * @param bool[][] $detectedCycles
     *
     * @return bool[][] de-duplicated
     */
    private static function deDuplicateDetectedCycles(array $detectedCycles): array
    {
        $detectedCyclesByHash = [];

        foreach ($detectedCycles as $detectedCycle) {
            $cycleAliases = array_keys($detectedCycle);

            sort($cycleAliases);

            $hash = serialize(array_values($cycleAliases));

            $detectedCyclesByHash[$hash] = isset($detectedCyclesByHash[$hash])
                ? $detectedCyclesByHash[$hash]
                : $detectedCycle;
        }

        return array_values($detectedCyclesByHash);
    }

    /**
     * @param string[] $aliases
     *
     * @return string
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
     * @param string[] $detectedCycle
     *
     * @return string
     */
    private static function printCycle(array $detectedCycle): string
    {
        $fullCycle = array_keys($detectedCycle);
        $fullCycle[] = reset($fullCycle);

        return implode(
            ' => ',
            array_map(
                function ($cycle) {
                    return '"' . $cycle . '"';
                },
                $fullCycle
            )
        );
    }
}
