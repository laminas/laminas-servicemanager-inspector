<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

use function sprintf;

final class MissingFactoryDetectedEvent implements TerminalEventInterface
{
    private string $dependencyName;

    public function __construct(string $dependencyName)
    {
        $this->dependencyName = $dependencyName;
    }

    public function getDependencyName(): string
    {
        return $this->dependencyName;
    }

    public function getError(): string
    {
        return sprintf("No factory is provided for '%s' service", $this->dependencyName);
    }
}
