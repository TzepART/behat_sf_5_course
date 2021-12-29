<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Doctrine\SchemaManager;
use App\Entity\Product;
use App\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    private User $currentUser;

    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private SchemaManager               $schemaManager
    ){}

    /**
     * @BeforeScenario
     */
    public function clearData(): void
    {
        $this->schemaManager->rebuildSchema();
    }

    /**
     * @BeforeScenario @fixtures
     */
    public function loadFixtures(): void
    {
        $this->schemaManager->loadFixtures();
    }

    /**
     * @Given there is an admin user :username with password :password
     */
    public function thereIsAnAdminUserWithPassword(string $username, string $password): User
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setUsername($username)
            ->setPassword($hashedPassword)
            ->setPlainPassword($password)
            ->setRoles(['ROLE_ADMIN']);

        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @When I fill in the search box with :term
     */
    public function iFillInTheSearchBoxWith(string $term): void
    {
        $searchBox = $this->assertSession()
            ->elementExists('css', 'input[name="searchTerm"]');

        $searchBox->setValue($term);
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton(): void
    {
        $button = $this->assertSession()
            ->elementExists('css', '#search_submit');

        $button->press();
    }

    /**
     * @Given there is/are :count product(s)
     */
    public function thereAreProducts(int $count): void
    {
        $this->createProducts($count);
    }

    /**
     * @Given I author :count products
     */
    public function iAuthorProducts(int $count): void
    {
        $this->createProducts($count, $this->currentUser);
    }

    /**
     * @Given the following product(s) exist(s):
     */
    public function theFollowingProductsExist(TableNode $table): void
    {
        foreach ($table as $row) {
            $product = (new Product())
                ->setName($row['name'])
                ->setPrice(rand(10, 1000))
                ->setDescription('lorem');

            if (isset($row['is published']) && $row['is published'] == 'yes') {
                $product->setIsPublished(true);
            }

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }

    /**
     * @Then the :rowText row should have a check mark
     */
    public function theProductRowShouldShowAsPublished(string $rowText): void
    {
        $row = $this->findRowByText($rowText);

        Assert::assertStringContainsString('fa-check', $row->getHtml(), 'Could not find the fa-check element in the row!');
    }

    /**
     * @When I press :linkText in the :rowText row
     */
    public function iClickInTheRow(string $linkText, string $rowText): void
    {
        $this->findRowByText($rowText)->pressButton($linkText);
    }

    /**
     * @When I click :linkName
     */
    public function iClick(string $linkName): void
    {
        $this->getPage()->clickLink($linkName);
    }

    /**
     * @Then I should see :count products
     */
    public function iShouldSeeProducts(int $count): void
    {
        $table = $this->getPage()->find('css', 'table.table');
        Assert::assertNotNull($table, 'Cannot find a table!');
        Assert::assertCount($count, $table->findAll('css', 'tbody tr'));
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin(): void
    {
        $this->currentUser = $this->thereIsAnAdminUserWithPassword('admin', 'admin');

        $this->visitPath('/login');
        $this->getPage()->fillField('Username', 'admin');
        $this->getPage()->fillField('Password', 'admin');
        $this->getPage()->pressButton('Login');
    }

    /**
     * @When I wait for the modal to load
     */
    public function iWaitForTheModalToLoad(): void
    {
        $this->getSession()->wait(
            5000,
            "$('.modal:visible').length > 0"
        );
    }

    /**
     * Saving a screenshot
     *
     * @When I save a screenshot to :filename
     */
    public function iSaveAScreenshotIn(string $filename)
    {
        sleep(1);
        $this->saveScreenshot($filename, __DIR__ . '/../behat_sf_5_course');
    }

    /**
     * @return DocumentElement
     */
    private function getPage(): DocumentElement
    {
        return $this->getSession()->getPage();
    }

    private function createProducts(int $count, User $author = null)
    {
        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setPrice(rand(10, 1000));
            $product->setDescription('lorem');

            if ($author) {
                $product->setAuthor($author);
            }

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }

    /**
     * @param string $rowText
     * @return NodeElement
     */
    private function findRowByText(string $rowText): NodeElement
    {
        $row = $this->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        Assert::assertNotNull($row, 'Cannot find a table row with this text!');

        return $row;
    }
}
