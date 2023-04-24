# Laravel Image Optimizer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/avnsh1111/laravel-image-optimizer.svg?style=flat-square)](https://packagist.org/packages/avnsh1111/laravel-image-optimizer)
[![Total Downloads](https://img.shields.io/packagist/dt/avnsh1111/laravel-image-optimizer.svg?style=flat-square)](https://packagist.org/packages/avnsh1111/laravel-image-optimizer)

Laravel Image Optimizer is a package that provides an easy way to optimize and resize images in your Laravel applications.

## Installation

You can install the package via composer:

```bash
composer require avnsh1111/laravel-image-optimizer
```

## Usage

To use the package, you need to create a model that uses the `OptimizeImage` trait. The trait provides a set of options for optimizing and resizing your images.

Here's an example of a model using the `OptimizeImage` trait:

```php
use LaravelImageOptimizer\Traits\OptimizeImage;
use Illuminate\Database\Eloquent\Model;

class TestImage extends Model
{
    use OptimizeImage;

    protected $fillable = ['name'];

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
}
```

### Options

- `name_only`: Set to `true` if you want to store just the file name in the database. Set to `false` if you want to store the full path.
- `base_path`: The base path where your images will be stored.
- `quality`: The quality of the optimized image (from 0 to 100).
- `width`: The maximum width of the optimized image.
- `height`: The maximum height of the optimized image.
- `keep_files`: Set to `true` if you want to keep the original image file when updating or deleting a record. Set to `false` if you want to remove the image file when updating or deleting a record.

### Example

Here's an example of how to use the package to optimize and resize an image:

```php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
```

## Testing

To run the tests, execute the following command:

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email [er.avinashrathod@gmail.com](mailto:er.avinashrathod@gmail.com) instead of using the issue tracker.

## Credits

- [Avinash Rathod](https://github.com/avnsh1111)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.