<?php
namespace FoxWorn3365\PHPExiled\NET;

class PromiseBucket {
    public static array $list = [];

    public $resolver;
    public ?string $uniqid;

    public function __construct(callable $resolver, ?string $uniqid) {
        $this->resolver = $resolver;
        $this->uniqid = $uniqid;

        self::$list[] = $this;
    }

    public function check(Bucket $bucket) : bool {
        if ($bucket->uniqid == $this->uniqid) {
            self::$list[array_search($this, self::$list)] = null;
            unset(self::$list[array_search($this, self::$list)]);
            ($this->resolver)($bucket->content);
            return true;
        }

        return false;
    }

    public static function checkAll(Bucket $bucket) : bool {
        foreach (self::$list as $promiseBucket) {
            if ($promiseBucket == null)
                continue;
            
            if ($promiseBucket->check($bucket))
                return true;
        }

        return false;
    }
}