<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

trait HasLocalizedOptions
{
    abstract public static function translationKey(): string;

    public function label(): string
    {
        return (string) trans('business.'.static::translationKey().'.'.$this->value);
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (static::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
