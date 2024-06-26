<?php
namespace FoxWorn3365\PHPExiled\CoreFeatures;

class Log {
    public static function debug(string $content) {
        if (!Settings::$debug)
            return;
        
        self::send($content, "debug", Colors::GREEN);
    }

    public static function info(string $content) {
        self::send($content, "info", Colors::CYAN);
    }

    public static function warn(string $content) {
        self::send($content, "warn", Colors::YELLOW);
    }

    public static function error(string $content) {
        self::send($content, "error", Colors::RED);
    }

    public static function send(string $content, string $name, string $color = Colors::MAGENTA) {
        echo "{$color}[" . date("d/m/Y - H:i:s") . "] [" . strtoupper($name) . "] >> {$content}" . Colors::WHITE . Colors::RESET . PHP_EOL; 
    }
}