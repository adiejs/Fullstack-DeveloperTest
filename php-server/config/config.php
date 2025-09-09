<?php
namespace App\Config;

return [
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'dbrestapi',
        'user' => 'dbuser',
        'pass' => 'dbpass',
        'charset' => 'utf8mb4'
    ],
    'jwt' => [
        'secret' => 'rahasia_super_aman',
        'issuer' => 'yourdomain.com',
        'aud' => 'yourdomain.com',
        'expire' => 3600
    ]
];