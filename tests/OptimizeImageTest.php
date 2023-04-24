<?php

use LaravelImageOptimizer\Traits\OptimizeImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Orchestra\Testbench\TestCase;

class OptimizeImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the image is optimized and resized correctly.
     */
    public function testOptimizesAndResizesImage()
    {
        // Fake storage for testing
        Storage::fake('public');

        // Create a test image
        $image = Image::make(UploadedFile::fake()->image('test-image.jpg', 2000, 2000));
        $imagePath = 'public/uploads/images/test-image.jpg';
        Storage::put($imagePath, (string)$image->encode());

        // Check if the image exists in storage
        $this->assertTrue(Storage::exists($imagePath));

        // Create a TestImage model
        $testImage = TestImage::create(['name' => 'test-image.jpg']);

        // Load the optimized image
        $optimizedImage = Image::make(Storage::get($imagePath));

        // Check if the optimized image dimensions are correct
        $this->assertLessThanOrEqual(800, $optimizedImage->width());
        $this->assertLessThanOrEqual(800, $optimizedImage->height());
    }

    /**
     * Test if the image file is kept when the keep_files property is set to true.
     */
    public function testKeepsImageFileOnUpdateOrDeleteWhenKeepFilesIsTrue()
    {
        // Fake storage for testing
        Storage::fake('public');

        // Create a test image
        $image = Image::make(UploadedFile::fake()->image('test-image.jpg', 2000, 2000));
        $imagePath = 'public/uploads/images/test-image.jpg';
        Storage::put($imagePath, (string)$image->encode());

        // Check if the image exists in storage
        $this->assertTrue(Storage::exists($imagePath));

        // Create a TestImage model
        $testImage = TestImage::create(['name' => 'test-image.jpg']);
        $testImage->keep_files = true;
        $testImage->delete();

        // Check if the image still exists in storage
        $this->assertTrue(Storage::exists($imagePath));
    }


    /**
     * Test if the image file is removed when the keep_files property is set to false.
     */
    public function testRemovesImageFileOnUpdateOrDeleteWhenKeepFilesIsFalse()
    {
        // Fake storage for testing
        Storage::fake('public');

        // Create a test image
        $image = Image::make(UploadedFile::fake()->image('test-image.jpg', 2000, 2000));
        $imagePath = 'public/uploads/images/test-image.jpg';
        Storage::put($imagePath, (string) $image->encode());

        // Check if the image exists in storage
        $this->assertTrue(Storage::exists($imagePath));

        // Create a TestImage model
        $testImage = TestImage::create(['name' => 'test-image.jpg']);
        $testImage->keep_files = false;
        $testImage->delete();

        // Check if the image is removed from storage
        $this->assertFalse(Storage::exists($imagePath));
    }
}

/**
 * TestImage model for testing the OptimizeImage trait.
 */
class TestImage extends Model
{
    use OptimizeImage;

    protected $fillable = ['name'];

    public $keep_files;

    protected static $optimizeImages = [
        'name' => [
            'name_only' => true,
            'base_path' => 'public/uploads/images/',
            'quality' => 85,
            'width' => 800,
            'height' => 800,
            'keep_files' => false // This value will be overridden by the $keep_files property
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Override the keep_files option with the value of the $keep_files property
        static::$optimizeImages['name']['keep_files'] = $this->keep_files;
    }
}
