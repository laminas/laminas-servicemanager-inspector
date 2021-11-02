<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner\Stub;

class ClassWithScalarParameterWithDefaultValue
{
    public function __construct(int $value = 1)
    {
    }
}
