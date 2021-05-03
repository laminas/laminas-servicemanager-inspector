<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

use function sprintf;

final class MissingFactoryDetectedEvent implements TerminalEventInterface
{
    /** @var string */
    private $dependencyName;

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
        return sprintf("No factory is provided for '%s' service.", $this->dependencyName);
    }
}
