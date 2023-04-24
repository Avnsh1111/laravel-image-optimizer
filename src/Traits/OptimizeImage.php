<?php

namespace Avnsh1111\LaravelImageOptimizer\Traits;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait OptimizeImage
{
    public static function bootOptimizeImage()
    {
        static::creating(function ($model) {
            $model->optimizeImages();
        });

        static::updating(function ($model) {
            $model->optimizeImages();
        });

        static::saved(function ($model) {
            $model->optimizeImages();
        });

        static::updated(function ($model) {
            $model->deleteImageFile();
        });

        static::deleted(function ($model) {
            $model->deleteImageFile();
        });
    }

    protected function optimizeImages()
    {
        foreach (static::$optimizeImages as $attribute => $config) {
            if ($this->attributes[$attribute] && $this->attributes[$attribute] != $this->getOriginal($attribute)) {
                $imagePath = $config['base_path'] . $this->attributes[$attribute];

                if ($config['name_only']) {
                    $imagePath = $config['base_path'] . $this->attributes[$attribute] . '.' . $this->guessExtension();
                }

                // Optimize and resize image
                $image = Image::make(Storage::path($imagePath));
                if (isset($config['quality'])) {
                    $image->encode(null, $config['quality']);
                }

                if (isset($config['width']) && isset($config['height'])) {
                    $image->resize($config['width'], $config['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Save the optimized image
                $image->save();

            }
        }
    }

    protected function deleteImageFile()
    {
        foreach (static::$optimizeImages as $attribute => $options) {
            $value = $this->getOriginal($attribute);

            if (!$value) {
                continue;
            }

            if (isset($options['keep_files']) && $options['keep_files']) {
                return;
            }

            if (isset($options['name_only']) && $options['name_only']) {
                $basePath = isset($options['base_path']) ? $options['base_path'] : '/';
                $imagePath = $basePath . $value;
            } else {
                $imagePath = $value;
            }

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }
}
