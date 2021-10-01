<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor;

final class NullConsoleColor implements ConsoleColorInterface
{
    public function success(string $text): string
    {
        return $text;
    }

    public function normal(string $text): string
    {
        return $text;
    }

    public function warning(string $text): string
    {
        return $text;
    }

    public function error(string $text): string
    {
        return $text;
    }

    public function critical(string $text): string
    {
        return $text;
    }
}
