<?php
namespace FoxWorn3365\PHPExiled\NET;

use FoxWorn3365\PHPExiled\CoreFeatures\Log;
use FoxWorn3365\PHPExiled\Enums\MessageType;
use FoxWorn3365\PHPExiled\Enums\DataType;

class Message {
    public string $raw_content;
    public int $receiver;
    public int $sender;
    public int $code;
    public string $uniqid;

    public null|array|object $content;

    public function __construct(string $raw_content, int $receiver, int $sender, int $code, string $uniqid = null) {
        $this->raw_content = $raw_content;
        $this->content = @json_decode($this->raw_content) ?? null;

        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->uniqid = $uniqid ?? uniqid();

        $this->code = $code;
    }

    public function encode() : string {
        if ($this->content == null) {
            return json_encode([
                "sender" => SocketClient::$id,
                "receiver" => 0,
                "code" => $this->code,
                "content" => $this->raw_content,
                "uniq_id" => $this->uniqid
            ]);
        }

        return json_encode([
            "sender" => SocketClient::$id,
            "receiver" => 0,
            "code" => $this->code,
            "content" => json_encode($this->content),
            "uniq_id" => $this->uniqid
        ]);
    }

    public static function createFromJson(string $json) : ?self {
        $data = json_decode($json);

        if (json_last_error() != JSON_ERROR_NONE) {
            Log::error("Failed to parse a message: the content throw a JSON error - " . json_last_error_msg());
            return null;
        }

        if (!self::validate($data)) {
            Log::error("Failed to parse a message: the validator rejected the content");
            return null;
        }

        return new self($data->content, (int)$data->receiver, (int)$data->sender, (int)$data->code, $data->uniq_id);
    }

    public static function validate(object $object) : bool {
        return isset($object->content) && isset($object->receiver) && isset($object->sender) && isset($object->code) && isset($object->uniq_id);
    }
}