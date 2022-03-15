<?php

declare(strict_types=1);

namespace App\Tests\Behat\Api\V1\Order;

use App\Entity\Order;
use App\Tests\Behat\Api\V1\ApiAwareContext;
use App\Tests\Fixtures\Api\V1\Order\Create\UserWithApiTokenFixture;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;

final class OrderContext extends ApiAwareContext
{
    /**
     * @Given /^I should see that Order "([^"]*)" exists in database$/
     */
    public function iShouldSeeThatOrderWithTypeAndIdExistsInDatabaseWithNoUser(string $code): void
    {
        /** @var Order $order */
        $order = $this->apiContext->entityExists(Order::class, ['code' => $code]);
//        $user = $order->getUser();

        Assert::assertTrue($order instanceof Order, sprintf('Order with code:"%s" does not exist.', $code));
    }

    /**
     * @Given /^User exists in database with apiToken "([^"]*)"$/
     */
    public function userWithApiTokenExistsInDatabase(string $apiToken): void
    {
        $this->apiContext->addFixture(
            new UserWithApiTokenFixture($apiToken, $this->apiContext->getUserPasswordHasher())
        );
        $this->apiContext->fixturesLoad();
    }

    /**
     * @When /^I make request "([A-Z]+)" "([^"]*)" with auth_token "([^"]*)" and user_id "([0-9]*)" with body$/
     */
    public function iMakeRequestWithAuthTokenUserIdAndBody(string $method, string $uri, string $apiToken, int $userId, PyStringNode $body): void
    {
        $this->apiContext->setAuthToken($apiToken);
        $this->apiContext->setUserId($userId);
        $this->apiContext->iMakeRequestWithBody($method, $uri, $body);
    }

}
