<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Inspector\EventReporter\ConsoleColor;

use function implode;
use function sprintf;

final class ConsoleColor implements ConsoleColorInterface
{
    private const RED_COLOR    = 1;
    private const GREEN_COLOR  = 2;
    private const YELLOW_COLOR = 3;
    private const WHITE_COLOR  = 7;

    public function success(string $text): string
    {
        return $this->open(self::GREEN_COLOR) . $text . $this->close();
    }

    public function normal(string $text): string
    {
        return $this->open(self::WHITE_COLOR) . $text . $this->close();
    }

    public function warning(string $text): string
    {
        return $this->open(self::YELLOW_COLOR) . $text . $this->close();
    }

    public function error(string $text): string
    {
        return $this->open(self::RED_COLOR) . $text . $this->close();
    }

    public function critical(string $text): string
    {
        return $this->open(self::WHITE_COLOR, self::RED_COLOR) . $text . $this->closeWithBackground();
    }

    private function open(int $foreground, ?int $background = null): string
    {
        $codes = ['3' . $foreground];
        if ($background !== null) {
            $codes[] = '4' . $background;
        }

        return sprintf("\033[%sm", implode(';', $codes));
    }

    private function close(bool $resetBackground = false): string
    {
        $resetCodes = [39];
        if ($resetBackground) {
            $resetCodes[] = 49;
        }

        return sprintf("\033[%sm", implode(';', $resetCodes));
    }

    private function closeWithBackground(): string
    {
        $resetCodes = [39, 49];

        return sprintf("\033[%sm", implode(';', $resetCodes));
    }
}
