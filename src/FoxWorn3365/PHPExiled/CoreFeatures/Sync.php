<?php
namespace FoxWorn3365\PHPExiled\CoreFeatures;

use FoxWorn3365\PHPExiled\Features\Player;
use FoxWorn3365\PHPExiled\NET\Message;
use FoxWorn3365\PHPExiled\NET\SocketClient;
use FoxWorn3365\PHPExiled\PHPExiled;

use function React\Async\async;

class Sync {
    public static function do() : void {
        PHPExiled::$loop->addPeriodicTimer(Settings::$refresh, async(static function() {
            Log::warn("Sync player!");
            if (Player::$schema == null) {
                Sync::playerSchema();
                return;
            }
        }));

        Sync::playerSchema();
    }

    public static function players() : void {
        return;
        $start = hrtime(true);
        $data = PHPExiled::$plugin->socket->syncSend(new Message("", 0, SocketClient::$id, 0x24c));
        Log::info("Took " . (hrtime(true) - $start)/1e+6 . "ms to get the complete player list");
        
        if ($data->code == 0x34c) {
            // Success, let's save the data
            if (Player::$list == []) {
                // Create the players instances
                foreach ($data->content as $playerData) {
                    new Player($playerData->Id, $playerData);
                }
            } else {
                $ids = [];
                foreach ($data->content as $playerData) {
                    $ids[] = (int)$playerData->Id;
                    $player = null;
                    if (Player::tryGet($playerData->Id, $player)) {
                        $player->override($playerData);
                    } else {
                        new Player($playerData->Id, $playerData);
                    }
                }

                // Now a check for exited players
                foreach (Player::$list as $player) {
                    if (!in_array($player->id, $ids)) {
                        $player->_destroy();
                    }
                }
            }
        }
    }

    public static function playerSchema() : void {
        $start = hrtime(true);
        $data = PHPExiled::$plugin->socket->syncSend(new Message("", 0, SocketClient::$id, 0x24cf));
        Log::info("Took " . (hrtime(true) - $start)/1e+6 . "ms to get the complete player list - WITH SCHEMA");
        
        if ($data->code == 0x34cf) {
            // Success, let's save the data
            if (Player::$list == []) {
                // Create the players instances
                foreach ($data->content as $playerData) {
                    Player::loadSchema($playerData);
                    new Player($playerData->Id->Value, $playerData);
                }
            } else {
                $ids = [];
                foreach ($data->content as $playerData) {
                    $ids[] = (int)$playerData->Id->Value;
                    $player = null;
                    if (Player::tryGet($playerData->Id->Value, $player)) {
                        $player->override($playerData);
                    } else {
                        new Player($playerData->Id->Value, $playerData);
                    }
                }

                // Now a check for exited players
                foreach (Player::$list as $player) {
                    if (!in_array($player->id, $ids)) {
                        $player->_destroy();
                    }
                }
            }
        }
    }
}