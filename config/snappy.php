<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary' => env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
        'timeout' => false,
        'options' => [
            'encoding' => 'UTF-8',
            'enable-javascript' => true,
            'javascript-delay' => 1000,
            'enable-smart-shrinking' => true,
            'no-stop-slow-scripts' => true,
            'disable-smart-shrinking' => false,
            'print-media-type' => true,
            'dpi' => 300,
            'image-quality' => 100,
        ],
    ],
    'image' => [
        'enabled' => true,
        'binary' => env('WKHTMLTOIMG_BINARY', '/usr/local/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [
            'encoding' => 'UTF-8',
            'enable-javascript' => true,
            'javascript-delay' => 1000,
            'enable-smart-shrinking' => true,
            'no-stop-slow-scripts' => true,
            'disable-smart-shrinking' => false,
            'print-media-type' => true,
            'dpi' => 300,
            'image-quality' => 100,
        ],
    ],
]; 