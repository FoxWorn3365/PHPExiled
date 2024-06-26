<?php
namespace FoxWorn3365\PHPExiled\CoreFeatures;

class Version {
    public readonly int $major;
    public readonly int $minor;
    public readonly int $build;

    public function __construct(int $major, int $minor = 0, int $build = 0) {
        $this->major = $major;
        $this->minor = $minor;
        $this->build = $build;
    }

    // Magic
    public function __toString() {
        return $this->toString();
    }

    public function toString() : string {
        return "{$this->major}.{$this->minor}.{$this->build}";
    }

    public function toNumber() : int {
        return (int)"{$this->major}{$this->minor}{$this->build}";
    }

    public function isNewer(Version $version) {
        return $this->toNumber() > $version->toNumber();
    }

    public function isOlder(Version $version) {
        return !$this->isNewer($version);
    }

    public function isSame(Version $version) {
        return $this->toNumber() == $version->toNumber();
    }

    public static function fromString(string $version) : self {
        $split = explode(".", $version);
        switch (count($split)) {
            case 1:
                return new self((int)$split[0]);
            case 2:
                return new self((int)$split[0], (int)$split[1]);
            case 3:
                return new self((int)$split[0], (int)$split[1], (int)$split[2]);
            default:
                return new self(1);
        }
    }
}