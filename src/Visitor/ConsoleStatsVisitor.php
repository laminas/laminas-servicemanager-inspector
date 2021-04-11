<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Visitor;

use function count;
use function in_array;
use function max;
use function printf;
use function str_repeat;

final class ConsoleStatsVisitor implements StatsVisitorInterface
{
    private const COLOR_GREEN  = "\e[1;32m";
    private const COLOR_YELLOW = "\e[33m";
    private const COLOR_RED    = "\e[0;31m";
    private const COLOR_END    = "\e[0m";

    /** @var int */
    private $maxDeep = 0;

    /** @var int */
    private $invokableCount = 0;

    /** @var int */
    private $autowireFactoryCount = 0;

    /** @var int */
    private $customFactoryCount = 0;

    /** @var array */
    private $invokableDependencies = [];

    /** @var array */
    private $autowiredDependencies = [];

    /** @var array */
    private $wiredDependencies = [];

    public function enterInvokable(string $dependencyName, array $instantiationStack): void
    {
        if (! in_array($dependencyName, $this->invokableDependencies, true)) {
            $this->invokableCount++;
            $this->collectDeep(count($instantiationStack));
        }

        $this->print($dependencyName, $instantiationStack);
    }

    public function enterAutowireFactory(string $dependencyName, array $instantiationStack): void
    {
        if (! in_array($dependencyName, $this->autowiredDependencies, true)) {
            $this->autowireFactoryCount++;
            $this->collectDeep(count($instantiationStack));
            $this->autowiredDependencies[] = $dependencyName;
        }

        $this->print($dependencyName, $instantiationStack);
    }

    public function enterCustomFactory(string $dependencyName, array $instantiationStack): void
    {
        if (in_array($dependencyName, $this->wiredDependencies, true)) {
            return;
        }

        $this->collectDeep(count($instantiationStack));
        $this->customFactoryCount++;
        $this->wiredDependencies[] = $dependencyName;
    }

    /**
     * @param array $instantiationStack
     */
    public function enterError(string $dependencyName, array $instantiationStack): void
    {
        printf(str_repeat('  ', count($instantiationStack)));
        printf("└─%s%s%s\n", self::COLOR_RED, $dependencyName, self::COLOR_END);
    }

    public function render(): void
    {
        printf(
            "\nFound factories: %s%s%s 🏭\n",
            self::COLOR_GREEN,
            $this->invokableCount + $this->autowireFactoryCount + $this->customFactoryCount,
            self::COLOR_END
        );
        printf(
            "Custom factories: %s%s%s 🛠️\n",
            self::COLOR_GREEN,
            $this->customFactoryCount,
            self::COLOR_END
        );
        printf(
            "Autowire factories: %s%s%s 🔥\n",
            self::COLOR_GREEN,
            $this->autowireFactoryCount,
            self::COLOR_END
        );
        printf(
            "Invokables: %s%s%s 📦\n",
            self::COLOR_GREEN,
            $this->invokableCount,
            self::COLOR_END
        );
        printf("\nMaximum instantiation deep: %s%s%s 🏊\n\n", self::COLOR_GREEN, $this->maxDeep, self::COLOR_END);
        printf("As far as I can tell, %sit's all good%s 🚀\n", self::COLOR_GREEN, self::COLOR_END);
    }

    private function collectDeep(int $deep): void
    {
        $this->maxDeep = max($deep, $this->maxDeep);
    }

    private function print(string $dependencyName, array $instantiationStack): void
    {
        $prefix = $suffix = '';
        if (count($instantiationStack) === 0) {
            $prefix = self::COLOR_YELLOW;
            $suffix = self::COLOR_END;
        }
        printf(str_repeat('  ', count($instantiationStack)));
        printf("└─%s%s%s\n", $prefix, $dependencyName, $suffix);
    }
}
