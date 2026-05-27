<?php
declare(strict_types=1);

class Validator {
    public static function required(mixed $value): bool {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        return !empty($value);
    }
    
    public static function email(string $value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function min(int $value, int $min): bool {
        return $value >= $min;
    }
    
    public static function max(int $value, int $max): bool {
        return $value <= $max;
    }
    
    public static function stringLength(string $value, int $min = 0, int $max = 255): bool {
        $len = mb_strlen($value, 'UTF-8');
        return $len >= $min && $len <= $max;
    }
}
