<?php
namespace FoxWorn3365\PHPExiled\NET;

use FoxWorn3365\PHPExiled\PHPExiled;

class MessageBuilder {
    public static function askConnection() : Message {
        return new Message("ok", 0, SocketClient::$id, 0x01);
    }

    public static function login() : Message {
        return new Message(json_encode(["key" => PHPExiled::$key]), 0, SocketClient::$id, 0x02);
    }

    public static function pluginData() : Message {
        return new Message(PHPExiled::$plugin->encode(), 0, SocketClient::$id, 0x03);
    }
}