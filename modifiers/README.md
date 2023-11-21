# Virtual Host Modifiers

Modifiers can be utilized to apply custom logic to VirtualHost entry like adding custom directives based on ServerName.

## Instructions

- Create a php file within the `modifiers` folder.

- Copy/Paste the following code and update it to match your requirement:

```php
<?php

return [
    'name' => 'Custom Error Log Path',
    'description' => 'Adds a custom error log path',
    'author' => 'wesam.ly',
    'callable' => function($host, $entries) {
        
        $directives = $host->getDirectivesAttribute();
        list($sld, $tld) = explode('.', $directives->get('ServerName'), 2);
        if ($tld == 'dev') {
            if (!$directives->has('ErrorLog')) {
                // TODO: check if DocumentRoot path exists and create a custom error log file
                $entries[] = 'ErrorLog "' . trim($directives->get('DocumentRoot'), '\'"') . '/error_log"';
            }
        }
        
        return $entries;
    },
    'priority' => 5,

];
```

## Useful Snippets

Virtual Host Directives as read from DB

`$directives = $host->getDirectivesAttribute();`

Virtual Host Tags

`$tags = $host->tags;`

Also check `app/Models/Host.php` for more functions.