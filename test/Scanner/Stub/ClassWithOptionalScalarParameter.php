<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner\Stub;

class ClassWithOptionalScalarParameter
{
    public function __construct(?int $value)
    {
    }
}
