<?php
namespace FoxWorn3365\PHPExiled\Enums;

use FoxWorn3365\PHPExiled\CoreFeatures\Enum;

class MessageType extends Enum {
    public const CONNECTION = "Connection";
    public const PING = "Ping";
    public const REQUEST = "Request";
    public const SMART_PING = "SmartPing";
    public const COMMUNICATION = "Communication";
    public const SERVER_EVENT = "ServerEvent";
    public const SERVER_REQUEST = "ServerRequest";
    public const CLIENT_EVENT_RESPONSE = "ClientEventResponse";
    public const REQUEST_EDIT = "RequestEdit";
    public const UNKNOWN = "Unknown";
}