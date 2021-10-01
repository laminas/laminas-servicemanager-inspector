<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

final class AutowireFactoryEnteredEvent implements EnterEventInterface
{
    /** @var string */
    private $dependencyName;

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

    /**
     * @psalm-return list<string>
     */
    public function getInstantiationStack(): array
    {
        return $this->instantiationStack;
    }
}
