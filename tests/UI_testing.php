<?php
declare(strict_types = 1);
require_once __DIR__ . '/../vendor/autoload.php';

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;
class UI_testing extends TestCase
{
   private $serverUrl = 'http://localhost:4444';
   private $driver;

   protected function setUp(): void{
       try {
           $capabilities = DesiredCapabilities::firefox();
           $capabilities->setCapability('marionette', true);

           $this->driver = RemoteWebDriver::create($this->serverUrl, $capabilities);
       } catch (Exception $e) {
           $this->fail("Failed to connect to Selenium server: " . $e->getMessage());
       }
   }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function testBadLogin(): void{
        $this->driver->get('http://localhost/landing_page.php');

        $wait = new WebDriverWait($this->driver, 10);
        $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('email'))
        );

        $emailField = $this->driver->findElement(WebDriverBy::name('email'));
        $emailField->clear();
        $emailField->sendKeys('wrong@email.com');

        $passwordField = $this->driver->findElement(WebDriverBy::name('password'));
        $passwordField->clear();
        $passwordField->sendKeys('wrong');

        try {
            $this->driver->findElement(WebDriverBy::name('login'))->click();
        } catch (Exception $e) {
            $this->fail("Failed to submit form: " . $e->getMessage());
        }

        $wait = new WebDriverWait($this->driver, 10);

        $errorElement = $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::xpath("//h2[contains(text(), 'INCORRECT EMAIL/PASSWORD')]")
            )
        );

        $this->assertNotNull($errorElement);
        $this->assertStringContainsString('INCORRECT EMAIL/PASSWORD', $errorElement->getText());

        $secondError = $this->driver->findElement(
            WebDriverBy::xpath("//h2[contains(text(), 'PLEASE TRY AGAIN')]")
        );
        $this->assertNotNull($secondError);
    }

    protected function tearDown(): void
    {
        $this->driver?->quit();
    }
}