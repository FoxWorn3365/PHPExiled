<?php
namespace FoxWorn3365\PHPExiled\Features;

use FoxWorn3365\PHPExiled\CoreFeatures\Log;
use FoxWorn3365\PHPExiled\CoreFeatures\Version;
use FoxWorn3365\PHPExiled\Events\Event;
use FoxWorn3365\PHPExiled\NET\SocketClient;
use FoxWorn3365\PHPExiled\PHPExiled;

use function React\Async\async;

class Plugin {
    public readonly int $id;
    public readonly string $author;
    public readonly string $name;
    public readonly Version $version;
    public readonly int $priority;
    public readonly array $events;
    public readonly string $prefix;
    public readonly ?Version $required_version;
    private readonly object $internal_events;

    public SocketClient $socket;

    public function __construct(int $id, string $name, string $author, Version $version, array $events = [], int $priority = 1, Version $required_version = null) {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->prefix = str_replace(" ", "_", strtolower($this->name));
        $this->version = $version;
        $this->events = $events;
        $this->priority = $priority;
        $this->required_version = $required_version;

        $this->internal_events = new \stdClass;

        PHPExiled::$plugin = $this;
    }

    public function run(string $ip, int $port) : void {
        Log::debug("Created plugin, requested socket run on {$ip}:{$port}");
        $this->socket = new SocketClient($ip, $port, PHPExiled::$key);
        $this->socket->connect();
        PHPExiled::$loop->run();
    }

    public function encode() : string {
        return json_encode([
            "id" => $this->id,
            "name" => $this->name,
            "prefix" => $this->prefix,
            "author" => $this->author,
            "version" => $this->version->toString(),
            "priority" => $this->priority,
            "subscribed_events" => implode("|", $this->events)
            /* "required_version" => $this->required_version->toString() */
        ]);
    }

    public function on(string $event, callable $action) {
        $this->internal_events->{$event} = async($action);
    }

    public function __tryCallEvent(string $event, Event $args) {
        if (isset($this->internal_events->{$event})) {
            ($this->internal_events->{$event})($args);
            $args->reply();
        }
    }

    public function __tryCall(string $event, $args = null) {
        Log::info("Trying to call function {$event} on plugin");
        if (isset($this->internal_events->{$event})) {
            Log::info("Function found!");
            if ($args == null) {
                ($this->internal_events->{$event})($this);
            } else {
                ($this->internal_events->{$event})($args);
            }
        }
    }
}