<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner\Stub;

class ClassWithNonExistingClassParameter
{
    public function __construct(NonExistingClass $service)
    {
    }
}
