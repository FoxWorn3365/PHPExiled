<?php
namespace FoxWorn3365\PHPExiled\Features;

use FoxWorn3365\PHPExiled\CoreFeatures\Attributes\EnumValue;
use FoxWorn3365\PHPExiled\CoreFeatures\Log;
use FoxWorn3365\PHPExiled\Enums\Game\AmmoType;
use FoxWorn3365\PHPExiled\Enums\Game\EffectType;
use FoxWorn3365\PHPExiled\Features\Items\Item;

class Player {
    public static array $list = [];
    public static ?object $schema = null;

    public int $id;

    /*
        $response = PHPExiled::$plugin->socket->syncSend(new Message(json_encode(["player" => $id]), 0, SocketClient::$id, 0x20));

        if ($response->code == 0x30) {
            // Success
            return new Player($id, $response->content);
        }

        Log::warn("Failed to retrive player {$id}!\nServer says 0x30e!");

        return null;
    */

    public static function loadSchema(object $playerData) {
        self::$schema = new \stdClass;
        foreach ($playerData as $key => $value) {
            self::$schema->{$key} = (object)[
                "type_name" => $value->TypeName,
                "type_full_name" => $value->TypeFullName
            ];
        }
    }

    public static function get(int $id) : ?Player {
        foreach (self::$list as $player) {
            if ($player->id == $id) {
                return $player;
            }
        }

        return null;
    }

    public static function tryGet(int $id, ?Player &$player) : bool {
        $player = self::get($id);
        return $player != null;
    }

    public static function __tryPut(int $id, object $data) : void {
        $player = null;
        if (self::tryGet($id, $player)) {
            $player->override($data);
            return;
        }

        new Player($id, $data);
    }

    public function __construct(int $id, object $data) {
        $this->id = $id;

        $this->override($data);

        self::$list[] = $this;
    }

    public function override(object $data) {
        foreach ($data as $key => $value) {
            $this->{strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key))} = $value->Value ?? $value;
        }
    }

    public function _destroy() {
        unset(self::$list[array_search($this, self::$list)]);
    }

    public function reloadWeapon() {
        $this->_invoke(__FUNCTION__, func_get_args());
    }

    public function handcuff(Player $owner = null) {
        $this->_invoke(__FUNCTION__, func_get_args());
    }

    public function removeHandcuffs() {

    }

    public function broadcast(int $duration, string $content) {

    }

    public function dropItem(Item $item, bool $throw) {

    }

    public function removeItem(Item $item) {

    }

    public function addItem(Item $item) {

    }

    public function sendConsoleMessage(string $message, string $color) {

    }

    public function disconnect(string $reason) {

    }

    public function resetStamina() {

    }

    public function getScpPreference(string $roleType) {

    }

    public function hurt(float $amount, Player $attacker = null, string $reason = "") {

    }

    public function useItem(Item $item) {

    }

    public function kill(string $reason) {

    }

    public function vaporize(Player $attacker = null) {

    }

    public function ban(int $duration, string $reason, Player $issuer = null) {
        $this->_invoke(__FUNCTION__, func_get_args());
    }

    public function kick(string $reason, Player $issuer = null) {

    }

    public function mute(bool $onlyIntercom = false) {

    }

    public function unmute(bool $onlyIntercom = false) {

    }

    public function remoteAdminMessage(string $message, bool $success = true, string $pluginName = null) {

    }

    public function sendStaffMessage(string $message) {

    }

    public function clearBroadcast() {

    }

    #[EnumValue(AmmoType::class, "ammoType")]
    public function addAmmo(string $ammoType, int $amount) {

    }

    #[EnumValue(AmmoType::class, "ammoType")]
    public function setAmmo(string $ammoType, int $amount) {

    }

    #[EnumValue(AmmoType::class, "ammoType")]
    public function getAmmo(string $ammoType) {

    }

    #[EnumValue(AmmoType::class, "ammoType")]
    public function dropAmmo(string $ammoType, int $amount) {

    }

    #[EnumValue(AmmoType::class, "ammoType")]
    public function getAmmoLimit(string $ammoType) {

    }

    public function clearInventory(bool $destroy = true) {

    }

    public function clearAmmo() {

    }

    public function showHint(string $content, float $duration) {

    }

    public function disableAllEffects() {
        
    }

    #[EnumValue(EffectType::class)]
    public function disableEffect(string $effectType) {

    }

    #[EnumValue(EffectType::class, "effectType")]
    public function enableEffect(string $effectType, float $duration, bool $addDurationIfActive = true) {

    }

    #[EnumValue(EffectType::class, "effectType")]
    public function changeEffectIntensity(string $effectType, float $intensity, float $duration) {

    }

    public function openReportWindow(string $text) {

    }

    public function addAhp(float $amount, float $limit = 75, float $decay = 1.2, float $efficacy = 0.7, float $sustain = 0, bool $persistant = false) {

    }

    public function reconnect(int $newPort = 0, float $delay = 5, bool $reconnect = true) {

    }

    private function _invoke(string $function, array $args) {
        var_dump($function, $args);
    }
}