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

    /*
    |--------------------------------------------------------------------------
    | Desktop Integration (Open in Finder/Explorer/IDE)
    |--------------------------------------------------------------------------
    |
    | EasyVHost can display an "Open Folder" button for each virtual host.
    | Because browsers are sandboxed, you must use a custom URL protocol
    | to bridge the browser to your local file system or IDE.
    |
    | If this value is null or empty, the button will be hidden.
    |
    | Usage:
    |   Define the full URL pattern using the %path% placeholder.
    |   The %path% will be automatically replaced with the project's root directory.
    |
    | Examples for .env:
    |
    |   1. Hammerspoon (macOS Recommended):
    |      EASYVHOST_DESKTOP_LINK_PATTERN="hammerspoon://openProject?path=%path%"
    |
    |   2. VS Code:
    |      EASYVHOST_DESKTOP_LINK_PATTERN="vscode://file/%path%"
    |
    |   3. PhpStorm:
    |      EASYVHOST_DESKTOP_LINK_PATTERN="phpstorm://open?url=file://%path%"
    |
    |   4. Custom Protocol (Windows/Linux):
    |      EASYVHOST_DESKTOP_LINK_PATTERN="easyvhost://%path%"
    |
    */

    'desktop_link_pattern' => env('EASYVHOST_DESKTOP_LINK_PATTERN', null),
];
