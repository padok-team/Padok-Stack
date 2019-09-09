<?php

declare(strict_types=1);

namespace App\Tests;

trait FixtureAwareCaseTrait
{
    protected static function loadFixtures(string $fixtureFileName): void
    {
        $loader = static::$kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $loader->load(['tests/fixtures/'.$fixtureFileName]);
    }
}
