<?php

namespace App\Factory;

use App\Entity\Shift;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Shift>
 */
final class ShiftFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    public static function class(): string
    {
        return Shift::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'date' => new \DateTimeImmutable(),
            'endTime' => new \DateTimeImmutable()->setTime(9, 0, 0),
            'startTime' => new \DateTimeImmutable()->setTime(17, 0, 0),
            'notes' => 'Some requirements to the shift'
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Shift $shift): void {})
        ;
    }
}
