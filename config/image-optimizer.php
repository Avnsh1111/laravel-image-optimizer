<?php

return [
    'optimizers' => [
        \Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '--strip-all',
            '--all-progressive',
        ],
        \Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force',
        ],
        \Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0',
            '-o2',
            '-quiet',
        ],
        \Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs',
        ],
        \Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b',
            '-O3',
        ],
    ],

    'log_optimizer_activity' => false,
];
