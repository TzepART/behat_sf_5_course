<?php

declare(strict_types=1);

namespace App\Tests\Behat\Api\V1\Order;

use App\Entity\Order;
use App\Entity\User;
use App\Tests\Behat\Api\V1\ApiAwareContext;
use App\Tests\Fixtures\Api\V1\Order\Create\ActivatedSmartOrdersFixture;
use App\Tests\Fixtures\Api\V1\Order\Create\ActivatedSmartTVWithUserAvatarFixture;
use App\Tests\Fixtures\Api\V1\Order\Create\UserFixture;
use PHPUnit\Framework\Assert;

final class OrderContext extends ApiAwareContext
{
    /**
     * @Given /^I should see that Order "([^"]*)" with id "([^"]*)" exists in database for passMediaId "([^"]*)"$/
     */
    public function iShouldSeeThatOrderWithTypeAndIdExistsInDatabaseForPassMediaId(string $type, string $originalId, string $passMediaId): void
    {
        $OrderId = Order::generateId($originalId, $type);
        /** @var Order $Order */
        $Order = $this->apiContext->entityExists(Order::class, ['id' => $OrderId]);
        $user = $Order->getUser();

        Assert::assertNotNull($user, sprintf('User not found for Order with type "%s" and id "%s".', $type, $originalId));

        /** @var User $user */
        $actualPassMediaId = $user->getPassMediaId();
        $invalidPassMediaIdMessage = sprintf(
            'Invalid passMediaId for Order with type "%s" and id "%s". Expected "%s", got "%s".',
            $type,
            $originalId,
            $passMediaId,
            $actualPassMediaId
        );
        Assert::assertEquals($actualPassMediaId, $passMediaId, $invalidPassMediaIdMessage);
    }

    /**
     * @Given /^I should see that Order "([^"]*)" with id "([^"]*)" exists in database with no user$/
     */
    public function iShouldSeeThatOrderWithTypeAndIdExistsInDatabaseWithNoUser(string $type, string $originalId): void
    {
        $OrderId = Order::generateId($originalId, $type);
        /** @var Order $Order */
        $Order = $this->apiContext->entityExists(Order::class, ['id' => $OrderId]);
        $user = $Order->getUser();

        Assert::assertNull($user, sprintf('Unexpected user for Order with type "%s" and id "%s".', $type, $originalId));
    }

    /**
     * @Given /^I should see that response matches spec$/
     */
    public function iShouldSeeThatResponseMatchesSpec(): void
    {
        $this->apiContext->iShouldSeeThatResponseMatchesSpec('api/Order');
    }

    /**
     * @Given /^activated smart Orders exists in database$/
     */
    public function activatedSmartOrdersExistsInDatabase(): void
    {
        $this->apiContext->addFixture(UserFixture::class);
        $this->apiContext->addFixture(ActivatedSmartOrdersFixture::class);
        $this->apiContext->fixturesLoad();
    }

    /**
     * @Given /^activated smart tv with profile avatar exists in database$/
     */
    public function activatedSmartTvWithProfileAvatarExistsInDatabase(): void
    {
        $this->apiContext->addFixture(UserFixture::class);
        $this->apiContext->addFixture(ActivatedSmartTVWithUserAvatarFixture::class);
        $this->apiContext->fixturesLoad();
    }

}
