<?php
namespace FoxWorn3365\PHPExiled\NET;

use FoxWorn3365\PHPExiled\CoreFeatures\Log;
use FoxWorn3365\PHPExiled\CoreFeatures\Sync;
use FoxWorn3365\PHPExiled\Enums\DataType;
use FoxWorn3365\PHPExiled\Enums\MessageType;
use FoxWorn3365\PHPExiled\Enums\SocketStatus;
use FoxWorn3365\PHPExiled\Events\Event;
use FoxWorn3365\PHPExiled\Features\Player;
use FoxWorn3365\PHPExiled\PHPExiled;
use React\Promise\Promise;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

use function React\Async\await;

class SocketClient {
    // Connection settings
    public string $ip;
    public int $port;
    private string $key;
    public static bool $hasReceivedWelcome = false;

    // Socket info
    public static string $status = SocketStatus::DISCONNECTED;

    // Socket props.
    public Connector $socket;
    public ConnectionInterface $interface;

    // The bucket is in it's static element

    // Public accessible elements
    public static int $id;

    public function __construct(string $ip, int $port, string $key) {
        $this->ip = $ip;
        $this->port = $port;
        $this->key = $key;

        $this->create();
    }

    private function create() {
        Log::debug("Created socket, trying to open on defined port.\nSHA256 key: " . hash('sha256', $this->key));
        $this->socket = new Connector(PHPExiled::$loop);
        Log::debug("Client created, trying to connect...");
        $this->interface = await($this->socket->connect("{$this->ip}:{$this->port}")->then(fn(ConnectionInterface $conn) => $conn, fn() => null));

        Log::info("Successfully connected to the server!");

        // Event handling
        $this->interface->on('close', fn() => self::$status = SocketStatus::DISCONNECTED);
        $this->interface->on('end', fn() => self::$status = SocketStatus::DISCONNECTED);
        $this->interface->on('data', fn($data) => self::received($data));

        self::$status = SocketStatus::CREATED;
    }

    public function connect() : void {
        if (self::$status != SocketStatus::CREATED) {
            Log::error("Error while connecting: the status is not SocketStatus::CREATED but <SocketStatus>" . self::$status);
            return;
        }

        Log::info("Creating connection pipe, awaiting for server Id...");
        await(new Promise(static function ($complete) {
            PHPExiled::$loop->addTimer(1, fn() => $complete(true));
        }));

        Log::info("Got server Id, asking for connection...");
        if ($this->syncSend(MessageBuilder::askConnection())->code == 0x10) {
            Log::info("Connection accepted, sending login informations...");
        } else {
            Log::error("Failed to connect to the EXILED socket server: connection request refused!");
            return;
        }

        self::$status = SocketStatus::AUTHING;

        if ($this->syncSend(MessageBuilder::login())->code == 0x11) {
            Log::info("Successfully logged-in, sending plugin informations...");
        } else {
            Log::error("Failed to log in, check your key!");
            return;
        }

        self::$status = SocketStatus::NEGOTIATING;

        if ($this->syncSend(MessageBuilder::pluginData())->code == 0x12) {
            Log::info("Plugin successfully registered, listening for events...");
        } else {
            Log::error("Failed to register plugin inside the EXILED socket server!");
            return;
        }

        self::$status = SocketStatus::CONNECTED;

        Sync::do();
        PHPExiled::$plugin->__tryCall('connected');
    }

    public function syncSend(Message $message) : ?Message {
        $obj = $this->send($message);

        if ($obj == null) {
            // Socket is not connected
            Log::error("Socket is no more connected, shutting down the client...");
            SocketClient::$status = SocketStatus::DISCONNECTED;
            PHPExiled::$loop->stop();
            die("\n_Terminated by library");
            return null;
        }

        return await($obj);
    }

    public function send(Message $message) : ?Promise {
        return $this->rawSend($message->encode(), $message->uniqid);
    }

    public function rawSend(string $message, ?string $uniqid = null) : ?Promise {
        if (self::$status == SocketStatus::DISCONNECTED) {
            return null;
        }
        
        $this->interface->write($message . "<EoM>");
        return new Promise(static function($resolver) use ($uniqid) {
            new PromiseBucket($resolver, $uniqid);
        });
    }

    public function rawRaySend(string $message) : void {
        $this->interface->write($message);
    }

    // Static function for socket usage
    public static function received($data) {
        Log::debug("Received message!\n{$data}");
        if (!self::$hasReceivedWelcome) {
            self::$id = Message::createFromJson(str_replace("<EoM>", "", $data))->receiver;
            Log::info("Successfully received welcome message!\nWe are client #" . self::$id);
            self::$hasReceivedWelcome = true;
            return;
        }

        if (Bucket::$open != null) {
            Bucket::$open->append($data);
            return;
        }

        $bucket = new Bucket();
        $bucket->append($data);

        if ($bucket->isCompleted) {
            if ($bucket->content->code == 0xe200) {
                // Received event, let's decode him
                $event = Event::createFromJson($bucket->content->raw_content);
                PHPExiled::$plugin->__tryCallEvent($event->name, $event);
            }
            else if ($bucket->content->code == 0xc20) {
                // Received player data (list)
                $ids = [];
                foreach ($bucket->content->content as $player) {
                    Player::__tryPut($player->Id, $player);
                    $ids[] = $player->Id;
                }

                foreach (Player::$list as $player) {
                    if (!in_array($player->id, $ids)) {
                        // Remove player
                        unset(Player::$list[array_search($player, Player::$list)]);
                    }
                }

                // Log::debug(json_encode($bucket->content->content));
            }
        }
    }
}