<?php
namespace FoxWorn3365\PHPExiled\CoreFeatures;

class Colors {
    public const BOLD = "\e[1m";
    public const DIM = "\e[2m";
    public const UNDERLINED = "\e[4m";
    public const BLINK = "\e[5m";
    public const INVERTED = "\e[7m";
    public const HIDDEN = "\e[8m";

    // Standard COLORS
    public const RED = "\e[31m";
    public const BLUE = "\e[34m";
    public const GREEN = "\e[32m";
    public const YELLOW = "\e[33m";
    public const MAGENTA = "\e[35m";
    public const CYAN = "\e[36m";
    public const BLACK = "\e[30m";
    public const WHITE = "\e[97m";

    // Standard BACKGROUNDS
    public const BACKGROUND_RED = "\e[41m";
    public const BACKGROUND_LIGHT_GRAY = "\e[47m";
    public const BACKGROUND_YELLOW = "\033[43m";
    public const BACKGROUND_BLACK = "\e[49m";
    public const BACKGROUND_GREEN = "\e[42m";
    public const BACKGROUND_LIGHT_BLUE = "\e[104m";
    public const DEFAULT = "\e[49m";
    public const DEFAULT_TEXT = "\e[39m";

    // Standard RESET
    public const RESET = "\e[0";
}