<?php

declare(strict_types=1);

namespace App\Tests\Assert;

use DMS\PHPUnitExtensions\ArraySubset\Constraint\ArraySubset;
use PHPUnit\Framework\Assert as PhpUnitAssert;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class AssertArray
{
    /**
     * Asserts that an array has a specified subset.
     *
     * @param iterable<mixed> $subset
     * @param iterable<mixed> $array
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public static function assertArraySubset(iterable $subset, iterable $array, bool $checkForObjectIdentity = false, string $message = ''): void
    {
        $constraint = new ArraySubset($subset, $checkForObjectIdentity);
        PhpUnitAssert::assertThat($array, $constraint, $message);
    }
}
