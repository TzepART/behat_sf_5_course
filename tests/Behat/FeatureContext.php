<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\Product;
use App\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    private $currentUser;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SymfonyFixturesLoader
     */
    private $symfonyFixturesLoader;

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * @var ORMExecutor
     */
    private $executor;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Loader $symfonyFixturesLoader,
        UserPasswordHasherInterface $passwordHasher
    ){
        $this->entityManager = $entityManager;
        $this->symfonyFixturesLoader = $symfonyFixturesLoader;
        $this->passwordHasher = $passwordHasher;
        $this->executor = new ORMExecutor($entityManager, new ORMPurger($entityManager));
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        $this->entityManager->createQuery('DELETE App:User u')->execute();
        $this->entityManager->createQuery('DELETE App:Product p')->execute();
    }

    /**
     * @BeforeScenario @fixtures
     */
    public function loadFixtures()
    {
        $this->executor->execute(
            $this->symfonyFixturesLoader->loadFromDirectory(__DIR__ . '/../../src/DataFixtures')
        );
    }

    /**
     * @Given there is an admin user :username with password :password
     */
    public function thereIsAnAdminUserWithPassword($username, $password): User
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

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @When I fill in the search box with :term
     */
    public function iFillInTheSearchBoxWith($term)
    {
        $searchBox = $this->assertSession()
            ->elementExists('css', 'input[name="searchTerm"]');

        $searchBox->setValue($term);
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton()
    {
        $button = $this->assertSession()
            ->elementExists('css', '#search_submit');

        $button->press();
    }

    /**
     * @Given there is/are :count product(s)
     */
    public function thereAreProducts($count)
    {
        $this->createProducts($count);
    }

    /**
     * @Given I author :count products
     */
    public function iAuthorProducts($count)
    {
        $this->createProducts($count, $this->currentUser);
    }

    /**
     * @Given the following product(s) exist(s):
     */
    public function theFollowingProductsExist(TableNode $table)
    {
        foreach ($table as $row) {
            $product = (new Product())
                ->setName($row['name'])
                ->setPrice(rand(10, 1000))
                ->setDescription('lorem');

            if (isset($row['is published']) && $row['is published'] == 'yes') {
                $product->setIsPublished(true);
            }

            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Then the :rowText row should have a check mark
     */
    public function theProductRowShouldShowAsPublished($rowText)
    {
        $row = $this->findRowByText($rowText);

        Assert::assertStringContainsString('fa-check', $row->getHtml(), 'Could not find the fa-check element in the row!');
    }

    /**
     * @When I press :linkText in the :rowText row
     */
    public function iClickInTheRow($linkText, $rowText)
    {
        $this->findRowByText($rowText)->pressButton($linkText);
    }

    /**
     * @When I click :linkName
     */
    public function iClick($linkName)
    {
        $this->getPage()->clickLink($linkName);
    }

    /**
     * @Then I should see :count products
     */
    public function iShouldSeeProducts($count)
    {
        $table = $this->getPage()->find('css', 'table.table');
        Assert::assertNotNull($table, 'Cannot find a table!');
        Assert::assertCount(intval($count), $table->findAll('css', 'tbody tr'));
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin()
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
    public function iWaitForTheModalToLoad()
    {
        $this->getSession()->wait(
            5000,
            "$('.modal:visible').length > 0"
        );
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )break
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) == '') {
        }
        fwrite(STDOUT, "\033[u");
        return;
    }

    /**
     * Saving a screenshot
     *
     * @When I save a screenshot to :filename
     */
    public function iSaveAScreenshotIn($filename)
    {
        sleep(1);
        $this->saveScreenshot($filename, __DIR__ . '/../behat_sf_5_course');
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    private function createProducts($count, User $author = null)
    {
        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setPrice(rand(10, 1000));
            $product->setDescription('lorem');

            if ($author) {
                $product->setAuthor($author);
            }

            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param $rowText
     * @return \Behat\Mink\Element\NodeElement
     */
    private function findRowByText($rowText)
    {
        $row = $this->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        Assert::assertNotNull($row, 'Cannot find a table row with this text!');

        return $row;
    }
}
