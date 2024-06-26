<?php
namespace FoxWorn3365\PHPExiled\Enums;

use FoxWorn3365\PHPExiled\CoreFeatures\Enum;

class DataType extends Enum {
    public const PLUGIN = "PluginData";
    public const CONNECTION = "ConnectionData";
    public const EVENT_UPDATE_REQUEST = "EventUpdateRequestData";
    public const REQUEST = "RequestData";
    public const UPDATE_PLUGIN = "UpdatePluginData";
    public const UNKNOWN = "Unknown";
    public const UPDATE_PLAYER = "UpdatePlayerData";
    public const UPDATE_ROOM = "UpdateRoomData";
    public const UPDATE_ITEM = "UpdateItemData";
    public const UPDATE_PICKUP = "UpdatePickupData";
    public const UPDATE_SERVER = "UpdateServerData";
    public const TRY_GET_PLAYER = "TryGetPlayer";
    public const TRY_GET_ITEM = "TryGetItem";
    public const TRY_GET_PICKUP = "TryGetPickup";
    public const TRY_GET_SERVER = "TryGetServer";
}