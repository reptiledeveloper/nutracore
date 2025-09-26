<?php
return [
    'pdf' => [
        'enabled' => true,
        'binary'  => '/usr/bin/wkhtmltopdf',
        'timeout' => false,
        'options' => [
            'page-width'  => '80mm',
            'page-height' => '200mm',
            'margin-top'    => 0,
            'margin-right'  => 0,
            'margin-bottom' => 0,
            'margin-left'   => 0,
        ],
    ],
    'image' => [
        'enabled' => true,
        'binary'  => '/usr/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => [],
    ],
];
