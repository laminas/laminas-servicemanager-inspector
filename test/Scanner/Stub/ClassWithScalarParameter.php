<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner\Stub;

class ClassWithScalarParameter
{
    public function __construct(int $value)
    {
    }
}
