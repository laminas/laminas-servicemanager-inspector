<?php

declare(strict_types=1);

namespace LaminasTest\ServiceManager\Inspector\EventReporter\ConsoleColor;

use Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\NullConsoleColor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor\NullConsoleColor
 */
class NullConsoleColorTest extends TestCase
{
    public function testSuccessReturnsUnchangedString()
    {
        $color   = new NullConsoleColor();
        $colored = $color->success('a');

        $this->assertSame('a', $colored);
    }

    public function testNormalreturnUnchangedString()
    {
        $color   = new NullConsoleColor();
        $colored = $color->normal('a');

        $this->assertSame('a', $colored);
    }

    public function testWarningReturnsUnchangedString()
    {
        $color   = new NullConsoleColor();
        $colored = $color->warning('a');

        $this->assertSame('a', $colored);
    }

    public function testErrorReturnUnchangedString()
    {
        $color   = new NullConsoleColor();
        $colored = $color->error('a');

        $this->assertSame('a', $colored);
    }

    public function testCriticalReturnsUnchangedString()
    {
        $color   = new NullConsoleColor();
        $colored = $color->critical('a');

        $this->assertSame('a', $colored);
    }
}
