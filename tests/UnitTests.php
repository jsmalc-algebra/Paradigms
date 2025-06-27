<?php
declare(strict_types = 1);
require_once '../functions.php';
require_once '../patterns/UserBuilderFactory.php';

use PHPUnit\Framework\TestCase;
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
        $test_user = UserBuilderFactory::createConsumptionTesterUserBuilder()
            ->setUserTier(2)
            ->setWeeklyPicturesUploaded(34)
            ->getUser();

        $this->assertSame(true,checkUserConsumption($test_user));
        $test_user->setWeeklyPicturesUploaded($test_user->getWeeklyPicturesUploaded() + 1);
        $this->assertSame(false,checkUserConsumption($test_user));

    }

    public function testFreeConsumption() : void{
        $test_user = UserBuilderFactory::createConsumptionTesterUserBuilder()
            ->setUserTier(1)
            ->setWeeklyPicturesUploaded(6)
            ->getUser();

        $this->assertSame(true,checkUserConsumption($test_user));
        $test_user->setWeeklyPicturesUploaded($test_user->getWeeklyPicturesUploaded() + 1);
        $this->assertSame(false,checkUserConsumption($test_user));
    }
}