<?php
declare(strict_types = 1);
require_once '../functions.php';
require_once '../patterns/UserBuilderFactory.php';
final class UnitTests extends \PHPUnit\Framework\TestCase
{
    public function testGoldUserConsumption() : void {
        $test_user = UserBuilderFactory::createConsumptionTesterUserBuilder()
            ->setUserTier(3)
            ->setWeeklyPicturesUploaded(555)
            ->getUser();

        $this->assertSame(true,checkUserConsumption($test_user));
    }
}