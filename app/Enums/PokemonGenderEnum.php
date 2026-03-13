<?php

namespace App\Enums;

enum PokemonGenderEnum: string
{
    case Male = 'male';
    case Female = 'female';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }

    public static function values(): array
    {
        return array_map(fn ($gender) => $gender->value, self::cases());
    }

    public static function default(): self
    {
        return self::Male;
    }
}
