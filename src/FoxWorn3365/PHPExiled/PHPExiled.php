<?php
namespace FoxWorn3365\PHPExiled;

use FoxWorn3365\PHPExiled\Features\Plugin;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

class PHPExiled {
    public static LoopInterface $loop;
    public static Plugin $plugin;
    public static string $key;

    public function __construct(string $key) {
        self::$loop = Loop::get();
        self::$key = $key;
    }
}