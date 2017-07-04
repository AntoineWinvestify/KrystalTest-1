<?php
namespace My; // Note the "My" namespace maps to the "tests" folder, as defined in the autoload part of `composer.json`.

use Facebook\WebDriver\WebDriverBy;
use Lmc\Steward\Test\AbstractTestCase;

class TitlePageTest extends AbstractTestCase {
    
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
