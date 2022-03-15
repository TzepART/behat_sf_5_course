<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Tests\Behat\Utils\Model\WireMockDto;
use App\Tests\Behat\Utils\WireMockHelper;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\ExampleNode;
use ReflectionClass;

class MockContext extends DbContext
{
    private const MOCKS_DIR_TEMPLATE = '%s/%s/mocks/%s';
    private const MOCK_FILE_TEMPLATE = '%s/%s.json';

    protected WireMockHelper $wireMockHelper;

    /**
     * @BeforeScenario
     */
    public function prepareWireMock(BeforeScenarioScope $scope): void
    {
        $this->wireMockHelper = new WireMockHelper();

        $mockFilePath = $this->getMockFilePath($scope);

        if (null !== $mockFilePath) {
            $mockData = json_decode((string) file_get_contents($mockFilePath), true);
            $scenario = $scope->getScenario();

            if ($scenario instanceof ExampleNode) {
                $key = $this->convertScenarioOutlineToMockDataKey($scenario);
                $mockData = $mockData[$key] ?? [];
            }

            foreach ($mockData as $row) {
                $this->setWireMockData($row);
            }
        }
    }

    /**
     * @AfterScenario
     */
    public function rollback(): void
    {
        $this->wireMockHelper->reset();
    }

    /**
     * @param mixed[] $data
     */
    protected function setWireMockData(array $data): void
    {
        $this->wireMockHelper->mockOutputRequest(WireMockDto::make($data));
    }

    protected function getMockDir(BeforeScenarioScope $scope): string
    {
        $reflector = new ReflectionClass(self::class);

        return sprintf(
            self::MOCKS_DIR_TEMPLATE,
            dirname((string) $reflector->getFileName()),
            $scope->getSuite()->getName(),
            $this->convertTitleToPathName($scope->getFeature()->getTitle())
        );
    }

    protected function convertTitleToPathName(?string $title): string
    {
        if (null === $title) {
            return '';
        }

        $dotPosition = mb_strpos($title, '.');
        $name = false !== $dotPosition ? mb_substr($title, 0, $dotPosition) : $title;

        return (string) preg_replace('/\s+/u', '_', $name);
    }

    protected function getMockFilePath(BeforeScenarioScope $scope): ?string
    {
        $scenario = $scope->getScenario();
        $scenarioTitle = $scenario instanceof ExampleNode ? $scenario->getOutlineTitle() : $scenario->getTitle();
        $mockFilePath = sprintf(
            self::MOCK_FILE_TEMPLATE,
            $this->getMockDir($scope),
            $this->convertTitleToPathName($scenarioTitle)
        );

        return file_exists($mockFilePath) ? $mockFilePath : null;
    }

    protected function convertScenarioOutlineToMockDataKey(ExampleNode $scenario): string
    {
        $tokens = $scenario->getTokens();

        return (string) reset($tokens);
    }
}
