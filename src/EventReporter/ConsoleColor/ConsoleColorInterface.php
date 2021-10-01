<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor;

interface ConsoleColorInterface
{
    public function success(string $text): string;

    public function normal(string $text): string;

    public function warning(string $text): string;

    public function error(string $text): string;

    public function critical(string $text): string;
}
