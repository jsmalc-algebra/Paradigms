<?php
declare(strict_types = 1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../MySQLiConfig.php';
require_once __DIR__ . '/../user.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../patterns/UserBuilderFactory.php';


final class UnitTests extends TestCase
{
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        // Clean up temp files after each test
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];
    }

    private function createTempImage($format)
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'gd_test_');
        $this->tempFiles[] = $tempPath;

        $image = imagecreatetruecolor(100, 100);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, 100, 100, $white);

        switch ($format) {
            case 'jpeg':
                imagejpeg($image, $tempPath);
                break;
            case 'png':
                imagepng($image, $tempPath);
                break;
            case 'gif':
                imagegif($image, $tempPath);
                break;
            case 'webp':
                imagewebp($image, $tempPath);
                break;
            case 'bmp':
                imagebmp($image, $tempPath);
                break;
        }

        imagedestroy($image);
        return $tempPath;
    }

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

    public function testConvertValidJPEG()
    {
        $tempPath = $this->createTempImage('jpeg');
        $result = convertToGD($tempPath);

        $this->assertNotFalse($result);
        $this->assertTrue(is_resource($result) || $result instanceof GdImage);

        if (is_resource($result) || $result instanceof GdImage) {
            imagedestroy($result);
        }
    }

    public function testConvertValidPNG()
    {
        $tempPath = $this->createTempImage('png');
        $result = convertToGD($tempPath);

        $this->assertNotFalse($result);
        $this->assertTrue(is_resource($result) || $result instanceof GdImage);

        if (is_resource($result) || $result instanceof GdImage) {
            imagedestroy($result);
        }
    }

    public function testConvertValidGIF()
    {
        $tempPath = $this->createTempImage('gif');
        $result = convertToGD($tempPath);

        $this->assertNotFalse($result);
        $this->assertTrue(is_resource($result) || $result instanceof GdImage);

        if (is_resource($result) || $result instanceof GdImage) {
            imagedestroy($result);
        }
    }

    public function testConvertValidWebP()
    {
        if (!function_exists('imagewebp')) {
            $this->markTestSkipped('WebP support not available');
        }

        $tempPath = $this->createTempImage('webp');
        $result = convertToGD($tempPath);

        $this->assertNotFalse($result);
        $this->assertTrue(is_resource($result) || $result instanceof GdImage);

        if (is_resource($result) || $result instanceof GdImage) {
            imagedestroy($result);
        }
    }
}