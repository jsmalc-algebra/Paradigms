<?php
declare(strict_types = 1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../ConnectToDatabase.php';
require_once __DIR__ . '/../user.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../patterns/UserBuilderFactory.php';


final class UnitTests extends TestCase
{
    public function testGoldUserConsumption() : void {
        $test_user = UserBuilderFactory::createConsumptionTesterUserBuilder()
            ->setUserTier(3)
            ->setWeeklyPicturesUploaded(555)
            ->getUser();

        $this->assertSame(true,checkUserConsumption($test_user));
    }
    public function testProConsumption() : void {
        $max_consumption_pro = 35;
        $test_user = UserBuilderFactory::createConsumptionTesterUserBuilder()
            ->setUserTier(2)
            ->setWeeklyPicturesUploaded($max_consumption_pro-1)
            ->getUser();

        $this->assertSame(true,checkUserConsumption($test_user));
        $test_user->setWeeklyPicturesUploaded($test_user->getWeeklyPicturesUploaded() + 1);
        $this->assertSame(false,checkUserConsumption($test_user));

    }

    public function testFreeConsumption() : void{
        $max_consumption_free =7;
        $test_user = UserBuilderFactory::createConsumptionTesterUserBuilder()
            ->setUserTier(1)
            ->setWeeklyPicturesUploaded($max_consumption_free-1)
            ->getUser();

        $this->assertSame(true,checkUserConsumption($test_user));
        $test_user->setWeeklyPicturesUploaded($test_user->getWeeklyPicturesUploaded() + 1);
        $this->assertSame(false,checkUserConsumption($test_user));
    }
}