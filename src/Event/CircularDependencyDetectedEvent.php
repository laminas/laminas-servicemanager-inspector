<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

use function implode;
use function sprintf;

final class CircularDependencyDetectedEvent implements TerminalEventInterface
{
    private string $dependencyName;

    /** @psalm-var list<string> */
    private $instantiationStack;

    /**
     * @psalm-param list<string> $instantiationStack
     * @param string[]  $instantiationStack
     */
    public function __construct(string $dependencyName, array $instantiationStack)
    {
        $this->dependencyName     = $dependencyName;
        $this->instantiationStack = $instantiationStack;
    }

    public function getDependencyName(): string
    {
        return $this->dependencyName;
    }

    public function getError(): string
    {
        return sprintf(
            'Circular dependency detected: %s -> %s',
            implode(' -> ', $this->instantiationStack),
            $this->dependencyName
        );
    }
}
