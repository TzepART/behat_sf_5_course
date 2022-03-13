<?php

declare(strict_types=1);

namespace App\Tests\Behat\Api\V1\Order;

use App\Entity\Order;
use App\Tests\Behat\Api\V1\ApiAwareContext;
use App\Tests\Fixtures\Api\V1\Order\Create\UserFixture;
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
     * @Given /^activated smart Orders exists in database$/
     */
    public function activatedSmartOrdersExistsInDatabase(): void
    {
        $this->apiContext->addFixture(UserFixture::class);
        $this->apiContext->fixturesLoad();
    }

}
