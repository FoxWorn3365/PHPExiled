<?php
namespace FoxWorn3365\PHPExiled\Enums;

use FoxWorn3365\PHPExiled\CoreFeatures\Enum;

class SocketStatus extends Enum {
    public const CREATED = "Created";
    public const CONNECING = "Connecting";
    public const AUTHING = "Authing";
    public const NEGOTIATING = "Negotiating";
    public const CONNECTED = "Connected";
    public const DISCONNECTED = "Disconnected";
}