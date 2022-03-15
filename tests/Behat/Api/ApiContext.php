<?php

declare(strict_types=1);

namespace App\Tests\Behat\Api;

use App\Tests\Assert\AssertArray;
use App\Tests\Behat\MockContext;
use Behat\Gherkin\Node\PyStringNode;
use JmesPath\Env;
use PHPUnit\Framework\Assert;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class ApiContext extends MockContext
{
    private KernelBrowser $client;
    /**
     * @var mixed[]|null
     */
    private ?array $responseJson = null;

    public function __construct(KernelBrowser $client)
    {
        parent::__construct($client->getKernel());

        $this->client = $client;
    }

    /**
     * @BeforeScenario
     */
    public function prepareClient(): void
    {
        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

    public function makeRequest(string $method, string $uri, ?string $content = null): void
    {
        $this->client->request($method, $uri, [], [], [], $content);
    }

    public function setAuthToken(string $authToken): void
    {
        $this->client->setServerParameter('HTTP_X_AUTH_TOKEN', $authToken);
    }

    public function setUserId(int $userId): void
    {
        $this->client->setServerParameter('HTTP_X_USER_ID', (string) $userId);
    }

    public function setHeader(string $name, string $value): void
    {
        $parameterName = sprintf('HTTP_%s', preg_replace('/-/', '_', mb_strtoupper($name)));
        $this->client->setServerParameter($parameterName, $value);
    }

    /**
     * @When /^I make request "([A-Z]+)" "([^"]*)"$/
     */
    public function iMakeRequest(string $method, string $uri): void
    {
        $this->makeRequest($method, $uri);
    }

    /**
     * @When /^I make request "([A-Z]+)" "([^"]*)" with body$/
     */
    public function iMakeRequestWithBody(string $method, string $uri, PyStringNode $body): void
    {
        $this->makeRequest($method, $uri, (string) $body);
    }

    /**
     * @Then /^I should see that response contains subset$/
     */
    public function iShouldSeeThatResponseContainsSubset(PyStringNode $expected): void
    {
        AssertArray::assertArraySubset(
            json_decode((string) $expected, true),
            $this->getJson()
        );
    }

    /**
     * @Then /^I should see that result contains "([^"]+)" with string not equals to "(.*)"$/
     */
    public function iShouldSeeThatResultContainsFieldWithStringNotEqualsTo(string $jmesPath, string $expected): void
    {
        $actual = $this->findInResponse($jmesPath);

        Assert::assertIsString($actual, sprintf('Failed asserting that value at path "%s" is string.', $jmesPath));
        $notEqualsErrorMessage = sprintf(
            'Failed asserting that actual string "%s" not equals to "%s" at path "%s".',
            $actual,
            $expected,
            $jmesPath
        );
        Assert::assertNotEquals($expected, $actual, $notEqualsErrorMessage);
    }

    /**
     * @Then /^I should see that result contains "([^"]+)" with string "(.*)"$/
     */
    public function iShouldSeeThatResultContainsFieldWithStringValue(string $jmesPath, string $expected): void
    {
        $actual = $this->findInResponse($jmesPath);
        Assert::assertIsString($actual, sprintf('Failed asserting that value at path "%s" is string.', $jmesPath));
        Assert::assertEquals(
            $expected,
            $actual,
            sprintf('Expected "%s" not equals to actual "%s" at path "%s".', $expected, $actual, $jmesPath)
        );
    }

    /**
     * @Then /^I should see that result contains "([^"]+)" with datetime$/
     */
    public function iShouldSeeThatResultContainsFieldWithDatetime(string $jmesPath): void
    {
        $actual = $this->findInResponse($jmesPath);
        Assert::assertTrue(
            is_string($actual) && false !== strtotime($actual),
            sprintf('Failed asserting that value at path "%s" is valid datetime.', $jmesPath)
        );
    }

    /**
     * @Then /^I should see that result contains "([^"]+)" with null$/
     */
    public function iShouldSeeThatResultContainsFieldWithNull(string $jmesPath): void
    {
        $actual = $this->findInResponse($jmesPath);
        Assert::assertNull($actual, sprintf('Failed asserting that value at path "%s" is null.', $jmesPath));
    }

    /**
     * @Then /^I should see that result contains "([^"]+)" with(| not empty)(| nullable) string$/
     */
    public function iShouldSeeThatResultContainsFieldWithString(string $jmesPath, string $notEmpty, string $nullable): void
    {
        $actual = $this->findInResponse($jmesPath);

        if ($nullable && null === $actual) {
            return;
        }

        Assert::assertIsString($actual, sprintf('Failed asserting that value at path "%s" is string.', $jmesPath));
        if ($notEmpty) {
            Assert::assertTrue('' !== $actual, sprintf('Failed asserting that value at path "%s" is not empty.', $jmesPath));
        }
    }

    /**
     * @Given /^I should see that result contains "([^"]*)" with(| nullable) int(?:eger)?(?: (>|<|=|<=|>=) (-?\d+)|()())$/
     */
    public function iShouldSeeThatResultContainsFieldWithInteger(string $jmesPath, string $nullable, string $op, string $expected): void
    {
        $actual = $this->findInResponse($jmesPath);

        if ($nullable && null === $actual) {
            return;
        }

        Assert::assertIsInt($actual, sprintf('Failed asserting that value at path "%s" is integer.', $jmesPath));

        if ($op) {
            $expected = (int) $expected;
            switch ($op) {
                case '>':
                    Assert::assertGreaterThan($expected, $actual, sprintf('Failed asserting that %s > %s at path "%s".', $actual, $expected, $jmesPath));
                    break;
                case '>=':
                    Assert::assertGreaterThanOrEqual($expected, $actual, sprintf('Failed asserting that %s >= %s at path "%s".', $actual, $expected, $jmesPath));
                    break;
                case '=':
                    Assert::assertEquals($expected, $actual, sprintf('Failed asserting that %s equals %s at path "%s".', $actual, $expected, $jmesPath));
                    break;
                case '<=':
                    Assert::assertLessThanOrEqual($expected, $actual, sprintf('Failed asserting that %s <= %s at path "%s".', $actual, $expected, $jmesPath));
                    break;
                case '<':
                    Assert::assertLessThan($expected, $actual, sprintf('Failed asserting that %s < %s at path "%s".', $actual, $expected, $jmesPath));
                    break;
                default:
                    throw new RuntimeException(sprintf('Could not be reached. Args: %s', json_encode(func_get_args(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
            }
        }
    }

    /**
     * @Given /^I should see that result contains "([^"]*)" with(| nullable) int(?:eger)? in range (\(|\[)(\d+),\s*(\d+)(\)|\])$/
     */
    public function iShouldSeeThatResultContainsFieldWithIntegerInRange(
        string $jmesPath,
        string $nullable,
        string $startIntervalType,
        string $from,
        string $to,
        string $endIntervalType
    ): void {
        $actual = $this->findInResponse($jmesPath);

        if ($nullable && null === $actual) {
            return;
        }

        Assert::assertIsInt($actual, sprintf('Failed asserting that value at path "%s" is integer.', $jmesPath));

        $from = (int) $from;
        $to = (int) $to;

        if ('(' === $startIntervalType) {
            Assert::assertGreaterThan($from, $actual, sprintf('Failed asserting that %s > %s at path "%s".', $actual, $from, $jmesPath));
        } elseif ('[' === $startIntervalType) {
            Assert::assertGreaterThanOrEqual($from, $actual, sprintf('Failed asserting that %s >= %s at path "%s".', $actual, $from, $jmesPath));
        }

        if (')' === $endIntervalType) {
            Assert::assertLessThan($to, $actual, sprintf('Failed asserting that %s < %s at path "%s".', $actual, $to, $jmesPath));
        } elseif (']' === $endIntervalType) {
            Assert::assertLessThanOrEqual($to, $actual, sprintf('Failed asserting that %s <= %s at path "%s".', $actual, $to, $jmesPath));
        }
    }

    /**
     * @return mixed
     */
    public function findInResponse(string $jmesPath)
    {
        $found = Env::search($jmesPath, $this->getJson());

        if (null === $found) {
            $keyParts = explode('.', $jmesPath);
            $key = array_pop($keyParts);
            $parentKey = implode('.', $keyParts);
            $notFoundErrorMessage = sprintf('Path "%s" not found in response.', $jmesPath);

            try {
                $existKeys = Env::search(sprintf('keys(%s)', $parentKey), $this->getJson());

                Assert::assertContains($key, $existKeys, $notFoundErrorMessage);
            } catch (RuntimeException $e) {
                throw new RuntimeException(sprintf('%s Cause: %s', $notFoundErrorMessage, $e->getMessage()));
            }
        }

        return $found;
    }

    /**
     * @Then /^I should see response json$/
     */
    public function iShouldSeeResponseJson(PyStringNode $expected): void
    {
        $actual = (string) $this->client->getResponse()->getContent();

        Assert::assertJson($actual);
        Assert::assertJsonStringEqualsJsonString((string) $expected, $actual);
    }

    /**
     * @Then /^I should see empty response body$/
     */
    public function iShouldSeeEmptyResponseBody(): void
    {
        $actual = (string) $this->client->getResponse()->getContent();

        Assert::assertEquals('', $actual);
    }

    /**
     * @Then /^I should see response json error (\d+) "(.*)"$/
     */
    public function iShouldSeeResponseJsonError(string $code, string $message): void
    {
        $actual = (string) $this->client->getResponse()->getContent();

        $expected = [
            'error' => [
                'code' => (int) $code,
                'message' => $message,
            ],
        ];

        Assert::assertJson($actual);
        Assert::assertJsonStringEqualsJsonString((string) json_encode($expected), $actual);
    }

    /**
     * @Then /^I should see response code (\d+)$/
     */
    public function iShouldSeeResponseCode(string $httpCode): void
    {
        $actual = $this->client->getResponse()->getStatusCode();

        Assert::assertEquals((int) $httpCode, $actual);
    }

    /**
     * @return mixed[]
     */
    public function getJson(): array
    {
        if (null === $this->responseJson) {
            $this->responseJson = json_decode((string) $this->client->getResponse()->getContent(), true);
        }

        return $this->responseJson;
    }
}
