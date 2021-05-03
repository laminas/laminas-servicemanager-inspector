<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventCollector;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function in_array;
use function max;
use function sprintf;
use function str_repeat;

final class ConsoleListener implements ListenerInterface
{
    private const COLOR_GREEN = "\e[1;32m";

    private const COLOR_YELLOW = "\e[33m";

    private const COLOR_RED = "\e[0;31m";

    private const COLOR_END = "\e[0m";

    /** @var OutputInterface */
    private $output;

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

    public function __construct()
    {
        $this->output = new NullOutput();
    }

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
        $this->output->write(sprintf(str_repeat('  ', count($instantiationStack))));
        $this->output->write(sprintf("└─%s%s%s\n", self::COLOR_RED, $dependencyName, self::COLOR_END));
    }

    public function render(): void
    {
        $this->output->write(
            sprintf(
                "\nTotal factories found: %s%s%s 🏭\n",
                self::COLOR_GREEN,
                $this->invokableCount + $this->autowireFactoryCount + $this->customFactoryCount,
                self::COLOR_END
            )
        );
        $this->output->write(
            sprintf(
                "Custom factories skipped: %s%s%s 🛠️\n",
                self::COLOR_GREEN,
                $this->customFactoryCount,
                self::COLOR_END
            )
        );
        $this->output->write(
            sprintf(
                "Autowire factories analyzed: %s%s%s 🔥\n",
                self::COLOR_GREEN,
                $this->autowireFactoryCount,
                self::COLOR_END
            )
        );
        $this->output->write(
            sprintf(
                "Invokables analyzed: %s%s%s 📦\n",
                self::COLOR_GREEN,
                $this->invokableCount,
                self::COLOR_END
            )
        );
        $this->output->write(
            sprintf(
                "\nMaximum instantiation deep: %s%s%s 🏊\n\n",
                self::COLOR_GREEN,
                $this->maxDeep,
                self::COLOR_END
            )
        );
        $this->output->write(
            sprintf(
                "As far as I can tell, %sit's all good%s 🚀\n",
                self::COLOR_GREEN,
                self::COLOR_END
            )
        );
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
        $this->output->write(sprintf(str_repeat('  ', count($instantiationStack))));
        $this->output->write(sprintf("└─%s%s%s\n", $prefix, $dependencyName, $suffix));
    }
}
