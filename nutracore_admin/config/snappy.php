<?php
return [
    'pdf' => [
        'enabled' => true,
        'binary'  => 'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
        'timeout' => false,
        'options' => [],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => '/usr/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => [],
    ],
];
