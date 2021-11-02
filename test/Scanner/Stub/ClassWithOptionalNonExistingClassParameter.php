<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner\Stub;

use NonExistingClass;

class ClassWithOptionalNonExistingClassParameter
{
    public function __construct(?NonExistingClass $service = null)
    {
    }
}
