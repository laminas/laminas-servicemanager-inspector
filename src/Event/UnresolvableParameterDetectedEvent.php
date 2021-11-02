<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

use function sprintf;

final class UnresolvableParameterDetectedEvent implements TerminalEventInterface
{
    /** @var string */
    private $dependencyName;

    /** @var string */
    private $paramName;

    public function __construct(string $dependencyName, string $paramName)
    {
        $this->dependencyName = $dependencyName;
        $this->paramName      = $paramName;
    }

    public function getDependencyName(): string
    {
        return $this->dependencyName;
    }

    public function getError(): string
    {
        return sprintf(
            "ReflectionBasedAbstractFactory cannot resolve parameter '%s' of '%s' service.",
            $this->paramName,
            $this->dependencyName
        );
    }
}
