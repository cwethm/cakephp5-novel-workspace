<?php
declare(strict_types=1);

namespace App\Domain\Registry;

final class CharacterProfileRegistry
{
    private const TRAIT_TYPES = [
        'strength' => 'Strength',
        'weakness' => 'Weakness',
        'habit' => 'Habit',
        'bad_habit' => 'Bad Habit',
        'fear' => 'Fear',
        'want' => 'Want',
        'need' => 'Need',
        'secret' => 'Secret',
        'personality' => 'Personality',
    ];

    private const GOAL_TYPES = [
        'external' => 'External',
    ];

    private const GOAL_STATUSES = [
        'active' => 'Active',
    ];

    /**
     * @return array<string, string>
     */
    public static function traitTypeOptions(): array
    {
        return self::TRAIT_TYPES;
    }

    /**
     * @return array<string, string>
     */
    public static function goalTypeOptions(): array
    {
        return self::GOAL_TYPES;
    }

    /**
     * @return array<string, string>
     */
    public static function goalStatusOptions(): array
    {
        return self::GOAL_STATUSES;
    }

    public static function isTraitType(string $value): bool
    {
        return isset(self::TRAIT_TYPES[$value]);
    }

    public static function isGoalType(string $value): bool
    {
        return isset(self::GOAL_TYPES[$value]);
    }

    public static function isGoalStatus(string $value): bool
    {
        return isset(self::GOAL_STATUSES[$value]);
    }
}
