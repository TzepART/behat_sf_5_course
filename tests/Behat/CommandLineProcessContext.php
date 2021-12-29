<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class CommandLineProcessContext implements Context
{
    private mixed $output;

    /**
     * @Given I have a file named :filename
     */
    public function iHaveAFileNamed($filename): void
    {
        touch($filename);
    }

    /**
     * @When I run :command
     */
    public function iRun($command): void
    {
        $this->output = shell_exec($command);
    }

    /**
     * @Then I should see :string in the output
     */
    public function iShouldSeeInTheOutput($string): void
    {
        Assert::assertStringContainsString(
            $string,
            $this->output,
            sprintf('Did not see "%s" in output "%s"', $string, $this->output)
        );
    }

    /**
     * @BeforeScenario
     */
    public function moveIntoTestDir(): void
    {
        if (!is_dir('test')) {
            mkdir('test');
        }
        chdir('test');
    }

    /**
     * @AfterScenario
     */
    public function moveOutOfTestDir(): void
    {
        chdir('..');
        if (is_dir('test')) {
            system('rm -r '.realpath('test'));
        }
    }

    /**
     * @Given I have a dir named :dir
     */
    public function iHaveADirNamed($dir): void
    {
        mkdir($dir);
    }
}
