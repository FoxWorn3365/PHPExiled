<?php

namespace FoxWorn3365\PHPExiled\CoreFeatures;


class Enum {
    public static function contains(string $value) : bool {
        foreach ((new \ReflectionClass(get_called_class()))->getConstants() as $_n => $content) {
            if ($content == $value) {
                return true;
            }
        }

        var_dump((new \ReflectionClass(get_called_class()))->getConstants());
        Log::debug("Error while parsing enum " . get_called_class() . ": Value {$value} not found!");
        return false;
    }

    public static function getValues() : array {
        return (new \ReflectionClass(get_called_class() ))->getConstants();
    }

    public static function parseFromNumber(int $number, int $startIndex = 0) : string {
        return @array_values((new \ReflectionClass(get_called_class()))->getConstants())[$number + $startIndex];
    }

    public static function parseToNumber(string $value) : int {
        $counter = -1;
        foreach ((new \ReflectionClass(get_called_class()))->getConstants() as $_n => $content) {
            $counter++;
            if ($content == $value) {
                return $counter;
            }
        }
    }
}