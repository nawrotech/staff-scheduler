<?php

namespace App\Factory;

use App\Entity\Assignment;
use App\Enum\AssignmentStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Assignment>
 */
final class AssignmentFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Assignment::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'assignedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'shift' => ShiftFactory::new(),
            'shiftPosition' => ShiftPositionFactory::new(),
            'staffProfile' => StaffProfileFactory::new(),
            'status' => self::faker()->randomElement(AssignmentStatus::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Assignment $assignment): void {})
        ;
    }
}
