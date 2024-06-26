<?php
namespace FoxWorn3365\PHPExiled\Events;

use FoxWorn3365\PHPExiled\CoreFeatures\Enum;
use FoxWorn3365\PHPExiled\CoreFeatures\Log;
use FoxWorn3365\PHPExiled\Enums\EventType;
use FoxWorn3365\PHPExiled\Features\Player;
use FoxWorn3365\PHPExiled\NET\Message;
use FoxWorn3365\PHPExiled\NET\SocketClient;
use FoxWorn3365\PHPExiled\PHPExiled;

class Event {
    public readonly string $name;
    public readonly string $event_type;
    public readonly bool $nullable;
    public readonly object $data;
    public readonly string $raw_data;
    public readonly string $uniqid;
    public bool $replied = false;

    public ?object $player;

    public function __construct(string $name, string|int $event_type, bool $nullable, string $raw, string $uniqid = null) {
        $this->name = $name;

        if (gettype($event_type) != "string") {
            $event_type = EventType::parseFromNumber($event_type, 1);
        }

        if (EventType::contains($event_type)) {
            $this->event_type = $event_type;
        } else {
            Log::warn("Failed to parse 'EventType': value {$event_type} not found!");
            $this->event_type = EventType::UNKNOWN;
        }

        $this->nullable = $nullable;
        $this->raw_data = $raw;
        $this->data = @json_decode($raw);
        $this->uniqid = $uniqid ?? uniqid();

        $this->import();
    }

    public static function createFromJson(string $json) : ?self {
        $data = json_decode($json);

        if (json_last_error() != JSON_ERROR_NONE) {
            Log::error("Failed to parse event!\nJson parse error: " . json_last_error_msg());
            return null;
        }

        /*
        if (isset($data->Name) && isset($data->EventType) && isset($data->Nullable) && isset($data->Data)) {
            return new self($data->Name, $data->EventType, $data->Nullable, json_encode($data->Data), @$data->UniqId);
        }
        */

        if (isset($data->name) && isset($data->event_type) && isset($data->nullable) && isset($data->data)) {
            if (isset($data->data->Player)) {
                Player::get($data->data->Player->Id)->override($data->data->Player);
            }
            return new self($data->name, $data->event_type, $data->nullable, json_encode($data->data), @$data->uniq_id);
        }

        Log::error("Failed to parse event\nGeneric parser error: missing key!");
        return null;
    }

    public function reply() {
        // Reply with a self istance
        $data = [
            "Name" => $this->name,
            "EventType" => $this->event_type,
            "Nullable" => $this->nullable,
            "UniqId" => $this->uniqid,
            "Data" => $this->data
        ];

        $this->replied = true;
        
        Log::info("Sending response for event {$this->name} to server...");
        PHPExiled::$plugin->socket->send(new Message(json_encode($data), 0, SocketClient::$id, 0xe200a));
        Log::info("Sent response for event {$this->name} to server!");
    }

    private function import() {
        if ($this->event_type == EventType::PLAYER_EVENT) {
            Player::__tryPut($this->data->Player->Id, $this->data->Player);
            $this->player = Player::get($this->data->Player->Id);
        }
    }
}