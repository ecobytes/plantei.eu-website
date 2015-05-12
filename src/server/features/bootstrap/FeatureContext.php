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
    }

    public static function setUpDb()
    {
        Artisan::call('migrate:install');
    }
    
    
    /**
     * @static
     * @beforeFeature
     */
    public static function prepDb()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed', array('--class' => 'SettingTableSeeder'));
        Artisan::call('db:seed', array('--class' => 'RolesTableSeeder'));
    }

    /**
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(Behat\Behat\Hook\Scope\AfterStepScope $scope)
    {
        if (99 === $scope->getTestResult()->getResultCode()) {
            $this->takeScreenshot($scope->getStep()->getText());
        }
    }

    private function takeScreenshot($sufix = '')
    {
        $driver = $this->getSession()->getDriver();
        /*if (!$driver instanceof Selenium2Driver) {
            return;
        }*/
        $baseUrl = $this->getMinkParameter('base_url');
        $fileName = date('d-m-y') . '-' . $sufix . '.png';
        $filePath = '/vagrant/test-results';

        $this->saveScreenshot($fileName, $filePath);
        print 'Saving screenshot at: test-results/'. $fileName;
    }
  
}
