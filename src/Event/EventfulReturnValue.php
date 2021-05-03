<?php

/**
 * @see       https://github.com/laminas/laminas-servicemanager-inspector for the canonical source repository
 * @copyright https://github.com/laminas/laminas-servicemanager-inspector/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-servicemanager-inspector/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\Command;

use Laminas\EventManager\EventInterface;
use Zakirullin\Mess\MessInterface;

final class EventfulReturnValue
{
    private $events;

    private $returnValue;

    public function __construct(array $events, ?$returnValue = null)
    {
        $this->events = $events;
        $this->returnValue = $returnValue;
    }

    public function getReturnValue(): MessInterface
    {
        return $this->mess;
    }

    /**
     * @return EventInterface[]
     */
    public function getEvents(): array
    {

    }
}
