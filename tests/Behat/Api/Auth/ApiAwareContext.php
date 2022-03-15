<?php

declare(strict_types=1);

namespace App\Tests\Behat\Api\Auth;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

abstract class ApiAwareContext implements Context
{
    protected ApiContext $apiContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope): void
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        /** @var ApiContext $apiContext */
        $apiContext = $environment->getContext(ApiContext::class);
        $this->apiContext = $apiContext;
    }
}
