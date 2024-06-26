<?php
namespace FoxWorn3365\PHPExiled\CoreFeatures\Attributes;

#[\Attribute]
class EnumValue {
    public string $class;
    public ?string $param;

    public function __construct(string $class, ?string $param = null) {
        $this->class = $class;
        $this->param = $param;
    }
}