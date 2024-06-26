<?php
namespace FoxWorn3365\PHPExiled\NET;

use FoxWorn3365\PHPExiled\CoreFeatures\Log;

class Bucket {
    public static array $list = [];
    public static ?Bucket $open = null;

    public ?Message $content = null;
    public string $bucket = "";
    public bool $isCompleted = false;
    public int $time;
    public ?string $uniqid;
    
    public function __construct(string $uniqid = null) {
        $this->uniqid = $uniqid;
        $this->time = strtotime("new");
        
        self::$list[] = $this;
    }

    public function append(string $data) : void {
        $this->bucket .= $data;

        if (self::$open != null && self::$open->isCompleted) {
            self::$open = null;
        }

        if (strpos($this->bucket, "<EoM>") !== false) {
            // Message finished!
            $this->isCompleted = true;
            file_put_contents($this->uniqid . '.txt', explode("<EoM>", $this->bucket)[0]);
            $this->content = Message::createFromJson(explode("<EoM>", $this->bucket)[0]);
            $this->uniqid = $this->content?->uniqid;
            Log::debug("Bucket for message [{$this->uniqid}] is complete!");

            if (self::$open == $this) {
                self::$open = null;
            }

            // Now let's check for the PromiseBucket
            if (PromiseBucket::checkAll($this)) {
                $this->delete();
                return;
            }
        }

        if (!$this->isCompleted) {
            self::$open = $this;
        }
    }

    public function delete() : void {
        self::$list[array_search($this, self::$list, true)] = null;
        unset(self::$list[array_search($this, self::$list, true)]);
    }

    // Static functions for the $list static thing
    public static function get(string $uniqid) : ?Bucket {
        foreach (self::$list as $bucket) {
            if ($bucket->uniqid == $uniqid) {
                return $bucket;
            }
        }

        return null;
    }
}