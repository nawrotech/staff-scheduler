<?php

namespace App\Enum;

enum StaffPositions: string
{
    case WAITER = 'Waiter';
    case CHEF = 'Chef';
    case BARTENDER = 'Bartender';
    case HOST = 'Host';
    case MANAGER = 'Manager';
    case CLEANER = 'Cleaner';
    
    public static function getAllPositions(): array
    {
        return array_map(
            fn(self $position) => $position->value,
            self::cases()
        );
    }
    
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $position) {
            $choices[$position->value] = $position->value;
        }
        return $choices;
    }
}