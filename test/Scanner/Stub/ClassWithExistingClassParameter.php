<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\Scanner\Stub;

use stdClass;

class ClassWithExistingClassParameter
{
    public function __construct(stdClass $obj)
    {
    }
}
