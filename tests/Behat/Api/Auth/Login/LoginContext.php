<?php

declare(strict_types=1);

namespace App\Tests\Behat\Api\Auth\Login;

use App\Tests\Behat\Api\Auth\ApiAwareContext;
use App\Tests\Fixtures\Api\Auth\Login\UserFixture;

class LoginContext extends ApiAwareContext
{
    /**
     * @Given /^several users exists in database$/
     */
    public function severalUsersExistsInDatabase(): void
    {
        $this->apiContext->addFixture(
            new UserFixture($this->apiContext->getUserPasswordHasher())
        );
        $this->apiContext->fixturesLoad();
    }
}