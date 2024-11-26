<?php

return [
    'addr_ports' => [
        'http' => '*:80',
        'https' => '*:443',
    ],

    'directives' => [
        'ServerName' => 'example.com',
        'DocumentRoot' => '"/var/www/html/example.com"',
        'ServerAdmin' => 'webmaster@localhost',
        'ServerAlias' => 'www.example.com',
        'ErrorLog' => '"/var/log/apache2/example.com-error.log"',
        'CustomLog' => '"/var/log/apache2/example.com-access.log" common',
        'TransferLog' => '"/var/log/apache2/example.com-access.log"',
    ],

    'main_directives' => ['ServerName', 'DocumentRoot'],

    'https_directives' => [
        'SSLCertificateFile' => '/etc/apache2/ssl/example.com.crt',
        'SSLCertificateKeyFile' => '/etc/apache2/ssl/example.com.key',
        'SSLCertificateChainFile' => '/etc/apache2/ssl/example.com.chain.crt',
    ],
];
