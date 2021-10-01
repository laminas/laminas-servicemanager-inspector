<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

interface EventInterface
{
    public function getDependencyName(): string;
}
