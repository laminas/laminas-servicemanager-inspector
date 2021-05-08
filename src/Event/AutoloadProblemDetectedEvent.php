<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

use function sprintf;

final class AutoloadProblemDetectedEvent implements TerminalEventInterface
{
    /** @var string */
    private $dependencyName;

    /** @var string */
    private $factoryClass;

    public function __construct(string $dependencyName, string $factoryClass)
    {
        $this->dependencyName = $dependencyName;
        $this->factoryClass = $factoryClass;
    }

    public function getDependencyName(): string
    {
        return $this->dependencyName;
    }

    public function getError(): string
    {
        return sprintf(
            "Cannot autoload factory class '%s' for service '%s'",
            $this->factoryClass,
            $this->dependencyName,
        );
    }
}
