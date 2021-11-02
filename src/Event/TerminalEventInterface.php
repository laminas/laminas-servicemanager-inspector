<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Event;

interface TerminalEventInterface extends EventInterface
{
    public function getError(): string;
}
