<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Laracasts\Behat\Context\Migrator;
use Laracasts\Behat\Context\DatabaseTransactions;



/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use Migrator;
    //use DatabaseTransactions;

    private $baseUrl = '';
    private $name = 'Test User';
    private $email = 'user@example.com';
    private $password = 'testpassword';


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
      App::environment('behat');
      $this->baseUrl = $this->getMinkParameter('base_url');
      $this->setUpDb();
  }

  public static function setUpDb()
  {
    Artisan::call('module:migrate-reset');
    Artisan::call('migrate');
    Artisan::call('module:migrate');
    Artisan::call('db:seed', array('--class' => 'SettingTableSeeder'));
    Artisan::call('db:seed', array('--class' => 'RolesTableSeeder'));
}


    /**
     * @AfterStep
     */
    /*public function takeScreenshotOnEachStep(Behat\Behat\Hook\Scope\AfterStepScope $scope)
    {
        $this->getSession()->resizeWindow(1280, 1024, 'current');
      $this->takeScreenshot($scope->getStep()->getText(), 'screenshots');
     }*/


    /**
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(Behat\Behat\Hook\Scope\AfterStepScope $scope)
    {
        if (99 === $scope->getTestResult()->getResultCode()) {
            $this->takeScreenshot($scope->getStep()->getText(), '');
        }
    }

    private function takeScreenshot($sufix = '', $dir)
    {
        $driver = $this->getSession()->getDriver();
        /*if (!$driver instanceof Selenium2Driver) {
            return;
        }*/
        $baseUrl = $this->getMinkParameter('base_url');
        $fileName = date('d-m-y') . '-' . $sufix . '.png';
        $filePath = '/vagrant/test-results/'.$dir;

        $this->saveScreenshot($fileName, $filePath);
        print 'Saving screenshot at: test-results/'. $fileName;
    }

    /**
     * @Given I have subscribed my email to the newsletter
     */
    public function iHaveSubscribedMyEmailToTheNewsletter()
    {
      $this->visit('/');
      $this->fillField('name', $this->name);
      $this->fillField('email', $this->email);
      $this->pressButton('Subscribe');
    }

    /**
     * @When I go to validation url
     */
    public function iGoToValidationUrl()
    {
        $subscriptor = \Modules\Newsletter\Entities\NewsletterSubscriptor::where('email', $this->email)->firstOrFail();
        $this->visit('/newsletter/confirm/'.$subscriptor->verification_key);
    }
  
}
