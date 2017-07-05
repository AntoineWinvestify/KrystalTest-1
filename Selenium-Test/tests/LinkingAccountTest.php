<?php
namespace My; // Note the "My" namespace maps to the "tests" folder, as defined in the autoload part of `composer.json`.

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverNavigation;
//use Facebook\WebDriver\WebDriverWait;
use Lmc\Steward\Test\AbstractTestCase;

class LinkingAccountTest extends AbstractTestCase {
    
    public function testLinkingComunitaeCorrect() {
        $website = 'http://cake_branch';
        
        // Load the URL (will wait until page is loaded)
        $this->wd->get($website); // $this->wd holds instance of \RemoteWebDriver
        $driver = $this->wd;
        $this->wd->findElement(WebDriverBy::cssSelector('#liLogin a'))->click();
        $this->wd->findElement(WebDriverBy::id("btnLoginUsername"))->sendKeys("inigo.iturburua@gmail.com");
        $this->wd->findElement(WebDriverBy::id("btnLoginPassword"))->sendKeys("8870mit");
        
        $this->wd->findElement(WebDriverBy::cssSelector('#loginBtn'))->click();
        $this->wd->wait()->until(
            WebDriverExpectedCondition::urlContains('marketplaces/showMarketPlace')
          );
        
        $url_linking = $website . "/investors/userProfileDataPanel";
        
        $this->wd->navigate()->to($url_linking);
        $driver->wait(5);
        $this->wd->findElement(WebDriverBy::id("linkedAccountsData"))->click();
        $this->wd->wait()->until(
            function () use ($driver) {
                $elements = $driver->findElements(WebDriverBy::id('addNewAccount'));

                return count($elements) > 0;
            },
            'Error locating more than one elements'
        );
        $this->wd->findElement(WebDriverBy::cssSelector('#addNewAccount'))->click();
        
        # get the select element	
       // $select = $this->wd->findElement(WebDriverBy::id('linkedaccount_companyId'))->selectOptionByValue('2');
        //$select = $driver->findElement("css selector", 'select[id="linkedaccount_companyId"] option[value="2"]');
        $select = $driver->findElement( WebDriverBy::id('linkedaccount_companyId') )
               ->findElement( WebDriverBy::cssSelector("option[value='2']") )
               ->click();

        # get all the options for this element
        /*$allOptions = $select->findElement(WebDriverBy::tagName('option'));

        # select the options
        foreach ($allOptions as $option) {
          $value = $option->getAttribute('value');
          if ($value == "2" || $value = 2) {
              $option->click();
              break;
          }
        }*/
        $this->wd->findElement(WebDriverBy::id("ContentPlaceHolder_userName"))->sendKeys("dssfg@gmail.com");
        $this->wd->findElement(WebDriverBy::id("ContentPlaceHolder_password"))->sendKeys("fgsdfg");
        $this->wd->findElement(WebDriverBy::id("linkNewAccount"))->click();
        $this->wd->wait()->until(
            function () use ($driver) {
                $elements = $driver->findElements(WebDriverBy::cssSelector('.box-warning strong'));

                return count($elements) > 0;
            },
            'Error locating more than one elements'
        );
        
        $messageError = $this->wd->findElement(WebDriverBy::cssSelector('#messageErrorLinkAccount strong'));
        $this->assertContains('incorrect', $messageError->getText());
        /*$this->wd->wait()->until(
            WebDriverExpectedCondition::urlContains('marketplaces/showMarketPlace')
          );*/
        
        // Do some assertion
        //$this->assertContains('Winvestify', $this->wd->getTitle());

        // You can use $this->log(), $this->warn() or $this->debug() with sprintf-like syntax
        /*$this->log('Current page "%s" has title "%s"', $this->wd->getCurrentURL(), $this->wd->getTitle());

        // Make sure search input is present
        $searchInput = $this->wd->findElement(WebDriverBy::cssSelector('#search-form input'));
        // Or you can use syntax sugar provided by Steward (this is equivalent of previous line)
        $searchInput = $this->findByCss('#search-form input');*/
        //$this->log($searchInput);
        // Assert title of the search input
        //$this->assertEquals('Search', $searchInput->getAttribute('title'));
        
    }
    
   /*public function testShouldContainSearchInput() {
        $website = 'https://www.w3.org/';
        // Load the URL (will wait until page is loaded)
        $this->wd->get($website); // $this->wd holds instance of \RemoteWebDriver

        // Do some assertion
        $this->assertContains('W3C', $this->wd->getTitle());

        // You can use $this->log(), $this->warn() or $this->debug() with sprintf-like syntax
        $this->log('Current page "%s" has title "%s"', $this->wd->getCurrentURL(), $this->wd->getTitle());

        // Make sure search input is present
        $searchInput = $this->wd->findElement(WebDriverBy::cssSelector('#search-form input'));
        // Or you can use syntax sugar provided by Steward (this is equivalent of previous line)
        $searchInput = $this->findByCss('#search-form input');
        //$this->log($searchInput);
        // Assert title of the search input
        $this->assertEquals('Search', $searchInput->getAttribute('title'));
    }*/
}
