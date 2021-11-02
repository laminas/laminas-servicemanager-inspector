<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Dependency;

final class Dependency
{
    /** @var string */
    private $name;

    /** @var bool */
    private $isOptional;

    public function __construct(string $name, bool $isOptional = false)
    {
        $this->name       = $name;
        $this->isOptional = $isOptional;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isOptional(): bool
    {
        return $this->isOptional;
    }
}
