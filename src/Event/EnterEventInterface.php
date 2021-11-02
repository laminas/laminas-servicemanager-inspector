<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

interface EnterEventInterface extends EventInterface
{
    /**
     * @psalm-return list<string>
     */
    public function getInstantiationStack(): array;
}
