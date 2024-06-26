<?php
require 'vendor/autoload.php';

use FoxWorn3365\PHPExiled\CoreFeatures\Log;
use FoxWorn3365\PHPExiled\CoreFeatures\Version;
use FoxWorn3365\PHPExiled\Events\Event;
use FoxWorn3365\PHPExiled\Features\Player;
use FoxWorn3365\PHPExiled\PHPExiled;
use FoxWorn3365\PHPExiled\Features\Plugin;

new PHPExiled("<YOURKEY>");
$plugin = new Plugin(10, "TestPlugin", "FoxWorn3365", new Version(1, 1, 0), ["Spawned", "Spawning", "TriggeringTesla"], 2);
$plugin->on('connected', static function(Plugin $plugin) {
    Log::error("connecvted");
    PHPExiled::$loop->addPeriodicTimer(10, static function() {
        Log::warn(count(Player::$list) . " connected players");
        foreach (Player::$list as $player) {
            Log::warn($player->nickname . " is connected!");
            Log::warn("{$player->nickname} has " . count($player->items) . " items!");
        }
    });

    $plugin->on('TriggeringTesla', static function(Event &$event) {
        Log::error("Received TriggeringTesla event!");
        Log::warn("{$event->player->nickname} has " . count($event->player->items) . " items!");
        // var_dump(Player::get($event->data->Player->Id)->role);
        if (count($event->player->items) > 2) {
            $event->data->IsAllowed = false;
            $event->data->DisableTesla = true;
        }
    });

    $plugin->on('Spawned', static function(Event &$event) {
        Log::error("Received Spawned event!");
        var_dump($event);
    });
});

$plugin->run("s1.fcosma.it", 7778);