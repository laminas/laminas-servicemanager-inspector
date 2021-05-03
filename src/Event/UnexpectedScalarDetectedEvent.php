<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

use function sprintf;

final class UnexpectedScalarDetectedEvent implements TerminalEventInterface
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
            "ReflectionBasedAbstractFactory cannot resolve scalar '%s' for '%s' service.",
            $this->paramName,
            $this->dependencyName
        );
    }
}
