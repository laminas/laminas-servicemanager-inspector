<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

final class CustomFactoryEnteredEventInterface implements EnterEventInterface
{
    /** @var string */
    private $dependencyName;

    /** @psalm-var list<string> */
    private $instantiationStack;

    public function __construct(string $dependencyName, array $instantiationStack)
    {
        $this->dependencyName     = $dependencyName;
        $this->instantiationStack = $instantiationStack;
    }

    public function getDependencyName(): string
    {
        return $this->dependencyName;
    }

    /**
     * @psalm-return list<string>
     */
    public function getInstantiationStack(): array
    {
        return $this->instantiationStack;
    }
}
